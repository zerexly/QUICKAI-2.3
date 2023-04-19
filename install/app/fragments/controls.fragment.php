        <form action="install.php" id="controls" class="step">
            <div class="form-errors color-red"></div>

            <div class="inner-wrapper">
                <div class="subsection">
                    <div class="section-title">License:</div>
                    
                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Purchase Code</label>
                            <div class="input-tip">
                                Enter random value.
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                           <input type="text" class="input required" name="key" value="">
                        </div>
                    </div>
                </div>

                <div class="subsection">
                    <div class="section-title">Database connection details:</div>
                    
                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Database Host</label>
                            <div class="input-tip">
                                You should be able to get this info from your 
                                web host, if localhost doesn't work
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="text" class="input required" name="db-host" value="localhost">
                        </div>
                    </div>

                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Database Name</label>
                            <div class="input-tip">
                                The name of the database you want to install QuickCMS in
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="text" class="input required" name="db-name" value="test">
                        </div>
                    </div>

                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Username</label>
                            <div class="input-tip">
                                Your MySQL username
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="text" class="input required" name="db-username" value="root">
                        </div>
                    </div>

                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Password</label>
                            <div class="input-tip">
                                Your MySQL password
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="password" class="input" name="db-password" value="">
                        </div>
                    </div>

                    <div class="clearfix mb-20">
                        <div class="col s12 m6 l6 mb-10">
                            <label class="form-label">Table Prefix</label>
                            <div class="input-tip">
                                If you want to run multiple installation in a single database,
                                change this
                            </div>
                        </div>

                        <div class="col s12 m6 m-last l6 l-last">
                            <input type="text" class="input" name="db-table-prefix" value="qa_">
                        </div>
                    </div>
                </div>
                
                <div class="subsection mb-0 install-only">
                    <div class="section-title">Administrative account details:</div>
                    
                    <div class="clearfix">
                        <div class="col s12 m6 l6 mb-20">
                            <label class="form-label">Full name *</label>
                            <input type="text" class="input required" name="user-fullname" value="Admin">
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="col s12 m6 l6 mb-20">
                            <label class="form-label">Username *</label>
                            <input type="text" class="input required" name="user-username" value="admin">
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="col s12 m6 l6 mb-20">
                            <label class="form-label">Email *</label>
                            <input type="text" class="input required" name="user-email" value="admin@gmail.com">
                        </div>

                        <div class="col s12 m6 m-last l6 l-last mb-20">
                            <label class="form-label">Password *</label>
                            <input type="password" class="input required" name="user-password" value="">
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="col s12 m6 l6 mb-20">
                            <label class="form-label">Time Zone *</label>
                            <select name="user-timezone" class="input required">
                                <?php foreach (getTimezones() as $k => $v): ?>
                                    <option value="<?= $k ?>" <?= $k == "UTC" ? "selected" : "" ?>><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="clearfix">
                        <div class="col s12 m6 l6 mb-20">
                            <label class="form-label">Default Country *</label>
                            <select name="user-country" class="input required">
                                <option value="">Select country</option>
                                <?php foreach (get_all_country_list() as $k => $v): ?>
                                    <option value="<?= strtolower($k) ?>" ><?= $v ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="gotonext mt-40">
                <div class="clearfix">
                    <div class="col s12 m6 offset-m3 m-last l4 offset-l4 l-last">
                        <input type="submit" value="Finish Installation" class="oval fluid button">
                    </div>
                </div>
            </div>
        </form>