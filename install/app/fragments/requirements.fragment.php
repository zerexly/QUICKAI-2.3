        <?php $installable = true; ?>
        <div id="requirements" class="step">
            <div class="subsection">
                <div class="section-title">1. Please configure PHP to match following requirements / settings:</div>

                <table>
                    <thead>
                        <tr>
                            <th>PHP Settings</th>
                            <th>Required</th>
                            <th>Current</th>
                            <th class="status">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><span class="fw-700">PHP Version</span></td>
                            <td>7.4.0+</td>
                            <td><?= PHP_VERSION ?></td>
                            <td class="status">
                                <?php if (version_compare(PHP_VERSION, '7.4.0') >= 0): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="fw-700">allow_url_fopen</span></td>
                            <td>On</td>
                            <td><?= ini_get("allow_url_fopen") ? "On" : "Off" ?></td>
                            <td class="status">
                                <?php if (ini_get("allow_url_fopen")): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="subsection">
                <div class="section-title">2. Please make sure following extensions are installed and enabled:</div>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Required</th>
                            <th>Current</th>
                            <th class="status">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <?php $curl = function_exists("curl_version") ? curl_version() : false; ?>
                            <td><span class="fw-700">cURL</span></td>
                            <td>7.19.4+</td>
                            <td><?= !empty($curl["version"]) ? $curl["version"] : "Not installed"; ?></td>
                            <td class="status">
                                <?php if (!empty($curl["version"]) && version_compare($curl["version"], '7.19.4') >= 0): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <?php 
                                $openssl = extension_loaded('openssl'); 
                                if ($openssl && !empty(OPENSSL_VERSION_NUMBER)) {
                                    $installed_openssl_version = get_openssl_version_number(OPENSSL_VERSION_NUMBER);
                                }
                            ?>
                            <td><span class="fw-700">OpenSSL</span></td>
                            <td>1.0.1c+</td>
                            <td><?= !empty($installed_openssl_version) ? $installed_openssl_version : "Outdated or not installed"; ?></td>
                            <td class="status">
                                <?php if (!empty($installed_openssl_version) && version_compare($installed_openssl_version, '1.0.1c') >= 0): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <?php $pdo = defined('PDO::ATTR_DRIVER_NAME'); ?>
                            <td><span class="fw-700">PDO</span></td>
                            <td>On</td>
                            <td><?= $pdo ? "On" : "Off"; ?></td>
                            <td class="status">
                                <?php if ($pdo): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <?php $gd = extension_loaded('gd') && function_exists('gd_info') ?>
                            <td><span class="fw-700">GD</span></td>
                            <td>On</td>
                            <td><?= $gd ? "On" : "Off"; ?></td>
                            <td class="status">
                                <?php if ($gd): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <?php $mbstring = extension_loaded('mbstring') && function_exists('mb_get_info') ?>
                            <td><span class="fw-700">mbstring</span></td>
                            <td>On</td>
                            <td><?= $mbstring ? "On" : "Off"; ?></td>
                            <td class="status">
                                <?php if ($mbstring): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <?php $exif = function_exists('exif_read_data') ?>
                            <td><span class="fw-700">EXIF</span></td>
                            <td>On</td>
                            <td><?= $exif ? "On" : "Off"; ?></td>
                            <td class="status">
                                <?php if ($exif): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            

            <div class="subsection">
                <div class="section-title">3. Please make sure following files and directories are writable:</div>

                <table>
                    <thead>
                        <tr>
                            <th>File</th>
                            <th class="status">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td><span class="fw-700">/install/</span></td>
                            <td class="status">
                                <?php if (is_writeable(ROOTPATH."/install/")): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-700">/install/database/</span></td>
                            <td class="status">
                                <?php if (is_writeable(ROOTPATH."/install/database/")): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fw-700">/includes/config.php</span></td>
                            <td class="status">
                                <?php if (is_writeable(ROOTPATH."/includes/config.php")): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                        <tr>
                            <td><span class="fw-700">/storage</span></td>
                            <td class="status">
                                <?php if (is_writeable(ROOTPATH."/storage")): ?>
                                    <span class="mdi mdi-check-circle color-green"></span>
                                <?php else: ?>
                                    <span class="mdi mdi-close-circle color-red"></span>
                                    <?php $installable = false; ?>
                                <?php endif ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <div class="gotonext">
                <?php if ($installable): ?>
                    <div class="clearfix">
                        <div class="col s12 m6 offset-m3 m-last l4 offset-l4 l-last">
                            <a href="javascript:void(0)" class="oval fluid button next-btn" data-next="#controls">Next</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="error color-red">
                        We are sorry! Your server configuration didn't match the application requirements!
                    </div>
                <?php endif; ?>
            </div>
        </div>