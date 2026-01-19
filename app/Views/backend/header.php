<!doctype html>
<html lang="en" data-bs-theme="dark">
<!--begin::Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>AdminLTE | Dashboard v3</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
    <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Primary Meta Tags-->
    <meta name="title" content="AdminLTE | Dashboard v3" />
    <meta name="author" content="ColorlibHQ" />
    <meta
        name="description"
        content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
    <meta
        name="keywords"
        content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
    <!--end::Primary Meta Tags-->

    <!--begin::Accessibility Features-->
    <!-- Skip links will be dynamically added by accessibility.js -->
    <meta name="supported-color-schemes" content="light dark" />
    <link rel="preload" href="<?= base_url('backend/css/adminlte.css') ?>" as="style" />
    <!--end::Accessibility Features-->

    <!--begin::Fonts-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
        crossorigin="anonymous"
        media="print"
        onload="this.media = 'all'" />
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="<?= base_url('backend/css/adminlte.css') ?>" />
    <!--end::Required Plugin(AdminLTE)-->

    <!--begin::Custom CSS-->
    <link rel="stylesheet" href="<?= base_url('backend/css/custom.css') ?>" />
    <!--end::Custom CSS-->

    <!-- apexcharts -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
        crossorigin="anonymous" />


    <!--font awesome-->
    <script src="https://use.fontawesome.com/4f75abfd27.js"></script>

    <!--Angular JS-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.6.3/angular.min.js" integrity="sha512-1qSuCpdoteBVxXOewHUBoVw+cvT1/kr+RuFK0HQsZFe6h7SEpm5VzXR1bBptPLyvQpxoaP/or5NUVRS4WwWgWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!--Jquery-->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

    <!--CK Editor-->
    <script type="text/javascript" src="<?= base_url("assets/ckeditor/ckeditor.js") ?>"></script>

    <!--Ngtable-->
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular-resource.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.4.2/angular-sanitize.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/ng-table@2.0.2/bundles/ng-table.min.css">
    <script src="https://unpkg.com/ng-table@2.0.2/bundles/ng-table.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.6/ui-bootstrap.js" integrity="sha512-3z5zbCPEG7DvKKz46yvPmKL+w+UDfwY0f2YWFVIwBb+2Y4E23jTZHZxG+naAiTllvMNQAhapPDKGHBT7V3fQOA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.6/ui-bootstrap-tpls.js" integrity="sha512-Re9KhAaoh5qo/Cm/wtExVs7ETTKTx/81aXPHko2nWlUvTzELYhTwpp/DwUu+z8ul+DjtbJdPcmxEYwKewzG62w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.6/ui-bootstrap-csp.css" integrity="sha512-TSigfdiJq7G9AWJnE/8D3M/HcBs9wfKpbrCbMg4iSs8IRVujA854B5wd/glfPzBRaeqiLLz1jHm6swYsci2txQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <!--begin::Container-->
            <div class="container-fluid">
                <!--begin::Start Navbar Links-->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="<?= base_url() ?>" class="nav-link">Frontend</a>
                    </li>
                </ul>
                <!--end::Start Navbar Links-->

                <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <!--begin::Fullscreen Toggle-->
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
                        </a>
                    </li>
                    <!--end::Fullscreen Toggle-->

                    <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img
                                src="<?= base_url('backend/assets/img/user2-160x160.jpg') ?>"
                                class="user-image rounded-circle shadow"
                                alt="User Image" />
                            <span class="d-none d-md-inline"><?= $my_user_data['name'] ?? '' ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <!--begin::User Image-->
                            <li class="user-header text-bg-secondary">
                                <img
                                    src="<?= base_url('backend/assets/img/user2-160x160.jpg') ?>"
                                    class="rounded-circle shadow"
                                    alt="User Image" />
                                <p>
                                    <?= $my_user_data['name'] ?? '' ?> - <?= $user_level_kv_list[$my_user_data['level']] ?? '' ?>
                                    <small>Member since <?= date('d M, Y', strtotime($my_user_data['created_date']))  ?></small>
                                </p>
                            </li>
                            <!--end::User Image-->

                            <!--begin::Menu Footer-->
                            <li class="user-footer">
                                <a href="<?= base_url(BACKEND_PORTAL . '/profile') ?>" class="btn btn-outline-secondary">Profile</a>
                                <a href="<?= base_url(BACKEND_PORTAL . '/logout') ?>" class="btn btn-outline-danger float-end">Sign out</a>
                            </li>
                            <!--end::Menu Footer-->
                        </ul>
                    </li>
                    <!--end::User Menu Dropdown-->
                </ul>
                <!--end::End Navbar Links-->
            </div>
            <!--end::Container-->
        </nav>
        <!--end::Header-->
        <!--begin::Sidebar-->
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <!--begin::Sidebar Brand-->
            <div class="sidebar-brand">
                <!--begin::Brand Link-->
                <a href="<?= base_url() ?>" class="brand-link">
                    <!--begin::Brand Image-->
                    <img
                        src="<?= base_url('backend/assets/img/AdminLTELogo.png') ?>"
                        alt="AdminLTE Logo"
                        class="brand-image opacity-75 shadow" />
                    <!--end::Brand Image-->
                    <!--begin::Brand Text-->
                    <span class="brand-text fw-light">AdminLTE 4</span>
                    <!--end::Brand Text-->
                </a>
                <!--end::Brand Link-->
            </div>
            <!--end::Sidebar Brand-->
            <!--begin::Sidebar Wrapper-->
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <!--begin::Sidebar Menu-->
                    <ul
                        class="nav sidebar-menu flex-column"
                        data-lte-toggle="treeview"
                        role="navigation"
                        aria-label="Main navigation"
                        data-accordion="false"
                        id="navigation">
                        <li class="nav-item">
                            <a href="<?= base_url(BACKEND_PORTAL . '/dashboard') ?>" class="nav-link <?= $current_module == 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-speedometer"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url(BACKEND_PORTAL . '/profile') ?>" class="nav-link <?= $current_module == 'profile' ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-person"></i>
                                <p>Profile</p>
                            </a>
                        </li>

                        <!--Setting-->
                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['setting']) && $my_permission_list['setting']['can_view'])): ?>
                            <li class="nav-item">
                                <a href="<?= base_url(BACKEND_PORTAL . '/setting') ?>" class="nav-link <?= $current_module == 'setting' ? 'active' : '' ?>">
                                    <i class="nav-icon bi bi-gear"></i>
                                    <p>Setting</p>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['setting']) && $my_permission_list['setting']['can_view'])): ?>
                            <li class="nav-item <?= $current_module == 'user' ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-people"></i>
                                    <p>
                                        User
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= base_url(BACKEND_PORTAL . '/user/list') ?>" class="nav-link <?= $current_module == 'user' && $current_page == 'list' ? 'active' : '' ?>">
                                            <i class="nav-icon bi bi-list"></i>
                                            <p>List</p>
                                        </a>
                                    </li>
                                    <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['user']) && $my_permission_list['user']['can_add'])): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url(BACKEND_PORTAL . '/user/add') ?>" class="nav-link <?= $current_module == 'user' && ($current_page == 'add' || $current_page == 'edit') ? 'active' : '' ?>">
                                                <i class="nav-icon bi bi-plus"></i>
                                                <p>Add</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!--Mail Template-->
                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['mail_template']) && $my_permission_list['mail_template']['can_view'])): ?>
                            <li class="nav-item <?= $current_module == 'mail_template' ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-gear-fill"></i>
                                    <p>
                                        Mail Template
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= base_url(BACKEND_PORTAL . '/mail_template/list') ?>" class="nav-link <?= $current_module == 'mail_template' && $current_page == 'list' ? 'active' : '' ?>">
                                            <i class="nav-icon bi bi-list"></i>
                                            <p>List</p>
                                        </a>
                                    </li>
                                    <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['mail_template']) && $my_permission_list['mail_template']['can_add'])): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url(BACKEND_PORTAL . '/mail_template/add') ?>" class="nav-link <?= $current_module == 'mail_template' && ($current_page == 'add' || $current_page == 'edit') ? 'active' : '' ?>">
                                                <i class="nav-icon bi bi-plus"></i>
                                                <p>Add</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!--Role-->
                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['role']) && $my_permission_list['role']['can_view'])): ?>
                            <li class="nav-item <?= $current_module == 'role' ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon bi bi-person-gear"></i>
                                    <p>
                                        Role
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= base_url(BACKEND_PORTAL . '/role/list') ?>" class="nav-link <?= $current_module == 'role' && $current_page == 'list' ? 'active' : '' ?>">
                                            <i class="nav-icon bi bi-list"></i>
                                            <p>List</p>
                                        </a>
                                    </li>
                                    <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['role']) && $my_permission_list['role']['can_add'])): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url(BACKEND_PORTAL . '/role/add') ?>" class="nav-link <?= $current_module == 'role' && ($current_page == 'add' || $current_page == 'edit') ? 'active' : '' ?>">
                                                <i class="nav-icon bi bi-plus"></i>
                                                <p>Add</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!--Enquiry-->
                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['enquiry']) && $my_permission_list['enquiry']['can_view'])): ?>
                            <li class="nav-item <?= $current_module == 'enquiry' ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa fa-comment"></i>
                                    <p>
                                        Enquiry
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= base_url(BACKEND_PORTAL . '/enquiry/list') ?>" class="nav-link <?= $current_module == 'enquiry' && $current_page == 'list' ? 'active' : '' ?>">
                                            <i class="nav-icon bi bi-list"></i>
                                            <p>List</p>
                                        </a>
                                    </li>
                                    <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['enquiry']) && $my_permission_list['enquiry']['can_add'])): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url(BACKEND_PORTAL . '/enquiry/add') ?>" class="nav-link <?= $current_module == 'enquiry' && ($current_page == 'add' || $current_page == 'edit') ? 'active' : '' ?>">
                                                <i class="nav-icon bi bi-plus"></i>
                                                <p>Add</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!--Page-->
                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['page']) && $my_permission_list['page']['can_view'])): ?>
                            <li class="nav-item <?= $current_module == 'page' ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa fa-file-text"></i>
                                    <p>
                                        Page
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= base_url(BACKEND_PORTAL . '/page/list') ?>" class="nav-link <?= $current_module == 'page' && $current_page == 'list' ? 'active' : '' ?>">
                                            <i class="nav-icon bi bi-list"></i>
                                            <p>List</p>
                                        </a>
                                    </li>
                                    <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['page']) && $my_permission_list['page']['can_add'])): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url(BACKEND_PORTAL . '/page/add') ?>" class="nav-link <?= $current_module == 'page' && ($current_page == 'add' || $current_page == 'edit') ? 'active' : '' ?>">
                                                <i class="nav-icon bi bi-plus"></i>
                                                <p>Add</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <!--Expenditure Category-->
                        <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['expenditure_category']) && $my_permission_list['expenditure_category']['can_view'])): ?>
                            <li class="nav-item <?= $current_module == 'expenditure_category' ? 'menu-open' : '' ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fa fa-cutlery"></i>
                                    <p>
                                        Expenditure Category
                                        <i class="nav-arrow bi bi-chevron-right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="<?= base_url(BACKEND_PORTAL . '/expenditure_category/list') ?>" class="nav-link <?= $current_module == 'expenditure_category' && $current_page == 'list' ? 'active' : '' ?>">
                                            <i class="nav-icon bi bi-list"></i>
                                            <p>List</p>
                                        </a>
                                    </li>
                                    <?php if (!$site_config['backend_check_permission'] || (isset($my_permission_list['expenditure_category']) && $my_permission_list['expenditure_category']['can_add'])): ?>
                                        <li class="nav-item">
                                            <a href="<?= base_url(BACKEND_PORTAL . '/expenditure_category/add') ?>" class="nav-link <?= $current_module == 'expenditure_category' && ($current_page == 'add' || $current_page == 'edit') ? 'active' : '' ?>">
                                                <i class="nav-icon bi bi-plus"></i>
                                                <p>Add</p>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <!--end::Sidebar Menu-->
                </nav>
            </div>
            <!--end::Sidebar Wrapper-->
        </aside>
        <!--end::Sidebar-->