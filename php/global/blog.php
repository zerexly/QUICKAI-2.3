<?php
// if blog is disable
if(!$config['blog_enable']){
    error(__("Page Not Found"), __LINE__, __FILE__, 1);
}

if(checkloggedin()) {
    update_lastactive();
}

if(!isset($_GET['page']))
    $page = 1;
else
    $page = $_GET['page'];

$limit = get_option('blog_page_limit', 8);
$search = $archive = $keyword = $category = $author = null;
$title = __("Blog");
$page_link = $link['BLOG'];
if(isset($_GET['archive'])){
    if($_GET['archive'] == 'category' && !empty($_GET['keyword'])){
        $archive_cat = ORM::for_table($config['db']['pre'] . 'blog_categories')
            ->where('slug', $_GET['keyword'])
            ->find_one();
        if(!empty($archive_cat['id'])){
            $category = $archive_cat['id'];
            $keyword = $archive_cat['title'];
        }else{
            error(__("Page Not Found"), __LINE__, __FILE__, 1);
            exit;
        }
    } else if($_GET['archive'] == 'author') {
        $archive_author = ORM::for_table($config['db']['pre'] . 'admins')
            ->where('username', $_GET['keyword'])
            ->find_one();
        if(!empty($archive_author['id'])){
            $author = $archive_author['id'];
            $keyword = $archive_author['name'];
        }else{
            error(__("Page Not Found"), __LINE__, __FILE__, 1);
            exit;
        }
    } else {
        error(__("Page Not Found"), __LINE__, __FILE__, 1);
        exit;
    }
}

$sql = "SELECT
b.*, u.name, u.username, u.image author_pic, GROUP_CONCAT(c.title) categories, GROUP_CONCAT(c.slug) cat_slugs
FROM `".$config['db']['pre']."blog` b
LEFT JOIN `".$config['db']['pre']."admins` u ON u.id = b.author
LEFT JOIN `" . $config['db']['pre'] . "blog_cat_relation` bc ON bc.blog_id = b.id
LEFT JOIN `" . $config['db']['pre'] . "blog_categories` c ON bc.category_id = c.id
WHERE b.status = 'publish'";

if($category){
    $sql .= " AND bc.category_id = $category";
    $title = __("Category").': '.$keyword;
    $page_link = $link['BLOG-CAT'].'/'.$_GET['keyword'];
}

if($author){
    $sql .= " AND b.author = $author";
    $title = __("Author").': '.$keyword;
    $page_link = $link['BLOG-AUTHOR'].'/'.$_GET['keyword'];
}

if(isset($_GET['s'])){
    $_GET['s'] = validate_input($_GET['s']);
    $search = str_replace("-"," ",$_GET['s']);
    $title = __("Search Results for").' &#8220;'.$search.'&#8221;';
    $sql .= " AND (b.title LIKE '%$search%' OR b.description LIKE '%$search%' OR b.tags LIKE '%$search%' )";
}

$sql .= " GROUP BY b.id ORDER BY b.created_at DESC";

$data = ORM::for_table($config['db']['pre'].'blog')->raw_query($sql)->find_many();
$total = count($data);

$query = "$sql LIMIT ".($page-1)*$limit.",$limit";

$result = ORM::for_table($config['db']['pre'].'blog')->raw_query($query)->find_many();

$items = array();
$result_found = count($result);
if ($result) {
    foreach ($result as $info)
    {
        $items[$info['id']]['id'] = $info['id'];
        $items[$info['id']]['title'] = $info['title'];
        $items[$info['id']]['image'] = !empty($info['image'])?$info['image']:'default.png';
        $items[$info['id']]['description'] = strlimiter(strip_tags(stripslashes($info['description'])),100);
        $items[$info['id']]['author'] = $info['name'];
        $items[$info['id']]['author_link'] = $link['BLOG-AUTHOR'].'/'.$info['username'];
        $items[$info['id']]['author_pic'] = !empty($info['author_pic'])?$info['author_pic']:'default_user.png';
        $items[$info['id']]['created_at'] = timeAgo($info['created_at']);
        $items[$info['id']]['link'] = $link['BLOG-SINGLE'].'/'.$info['id'].'/'.create_slug($info['title']);

        $categories = explode(',',$info['categories']);
        $cat_slugs = explode(',',$info['cat_slugs']);
        $arr = array();
        for($i = 0; $i < count($categories); $i++){
            $arr[] = '<a href="'.$link['BLOG-CAT'].'/'.$cat_slugs[$i].'">'.$categories[$i].'</a>';
        }
        $items[$info['id']]['categories'] = implode(', ',$categories);

    }
}

$pagging = pagenav($total,$page,$limit,$page_link);
if($search){
    $pagging = pagenav($total,$page,$limit,$page_link.'?s='.$search,1);
}

// get categories
$sql = "SELECT
c.*, COUNT(bc.blog_id) blog
FROM `".$config['db']['pre']."blog_categories` c
LEFT JOIN `" . $config['db']['pre'] . "blog_cat_relation` bc ON bc.category_id = c.id
LEFT JOIN `" . $config['db']['pre'] . "blog` b ON bc.blog_id = b.id
WHERE c.active = '1' AND b.status = 'publish' GROUP BY c.id ORDER BY c.position";
$result = ORM::for_table($config['db']['pre'].'blog_categories')->raw_query($sql)->find_many();
$blog_cat = array();
foreach($result as $row){
    $blog_cat[$row['id']]['id'] = $row['id'];
    $blog_cat[$row['id']]['title'] = $row['title'];
    $blog_cat[$row['id']]['blog'] = $row['blog'];
    $blog_cat[$row['id']]['link'] = $link['BLOG-CAT'].'/'.$row['slug'];
}

// get testimonials
$rows = ORM::for_table($config['db']['pre'] . 'testimonials')
    ->order_by_desc('id')
    ->limit(5)
    ->find_many();
$testimonials = array();
foreach ($rows as $row) {
    $testimonials[$row['id']]['id'] = $row['id'];
    $testimonials[$row['id']]['name'] = $row['name'];
    $testimonials[$row['id']]['designation'] = $row['designation'];
    $testimonials[$row['id']]['content'] = $row['content'];
    $testimonials[$row['id']]['image'] = !empty($row['image']) ? $row['image'] : 'default_user.png';
}

// get all tags
$rows = ORM::for_table($config['db']['pre'].'blog')
    ->select('tags')
    ->where('status', 'publish')
    ->find_many();
$all_tags = array();
$tag2 = array();
foreach($rows as $row){
    if(!empty($row['tags'])) {
        $tag = explode(',', $row['tags']);
        foreach ($tag as $val) {
            //REMOVE SPACE FROM $VALUE ----
            $tagTrim = preg_replace("/[\s_]/", "-", trim($val));
            $tag2[] = '<a href="' . $link['BLOG'] . '?s=' . $tagTrim . '"><span>' . $val . '</span></a>';
        }
    }
}
$all_tags = implode('  ', array_unique($tag2));
//Print Template
HtmlTemplate::display('global/blog', array(
    'title' => $title,
    'search' => $search,
    'result_found' => $result_found,
    'all_tags' => $all_tags,
    'show_paging' => (int)($total > $limit),
    'items' => $items,
    'pagging' => $pagging,
    'blog_cat' => $blog_cat,
    'testimonials' => $testimonials
));
exit;