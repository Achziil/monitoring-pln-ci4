<?php

use CodeIgniter\Router\RouteCollection;

// mematikan auto routes
$routes->setAutoRoute(false);

/**
 * @var RouteCollection $routes
 */

// Authentication Routes
$routes->get('/', 'AuthController::login', ['as' => 'log-in', 'filter' => 'authCheck:guest']);
$routes->get('/login', 'AuthController::login', ['as' => 'login', 'filter' => 'authCheck:guest']);
$routes->post('/auth', 'AuthController::authenticate', ['as' => 'authenticate']);
$routes->get('/logout', 'AuthController::logout', ['as' => 'logout']);

// sidebar dashboard
$routes->get('dashboard', 'DashboardController::index', ['as' => 'dashboard']);

// Grouping routes by roles and applying the AuthCheck filter
// ------------------------------ ADMIN ---------------------------------
$routes->group('admin', ['filter' => 'authCheck:admin'], function ($routes) {
    // dashboard admin
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'admin.dashboard']);
    $routes->get('dashboard/top-category-realisasi', 'DashboardController::getTopCategoryRealisasiAjax');

    // categories
    $routes->get('categories', 'CategoriesController::index', ['as' => 'categories.index']);
    $routes->post('categories/create', 'CategoriesController::create', ['as' => 'categories.create']);
    $routes->post('categories/edit/(:num)', 'CategoriesController::edit/$1', ['as' => 'categories.edit']);
    $routes->post('categories/delete/(:num)', 'CategoriesController::delete/$1', ['as' => 'categories.delete']);

    // users
    $routes->get('users', 'UsersController::index', ['as' => 'users.index']);
    $routes->post('users/create', 'UsersController::create', ['as' => 'users.create']);
    $routes->post('users/edit/(:num)', 'UsersController::edit/$1', ['as' => 'users.edit']);
    $routes->get('users/view/(:num)', 'UsersController::view/$1', ['as' => 'users.view']);
    $routes->post('users/delete/(:num)', 'UsersController::delete/$1', ['as' => 'users.delete']);

    // edit profile
    $routes->get('edit_profile', 'UsersController::editProfile', ['as' => 'edit_profile']);
    $routes->post('update_profile', 'UsersController::updateProfile', ['as' => 'update_profile']);

    // $routes->get('users/edit_profile', 'UsersController::editProfile');
    // $routes->post('users/update_profile', 'UsersController::updateProfile');

    // Sarana routes
    $routes->get('sarana', 'SaranaController::index', ['as' => 'sarana.index']);
    $routes->get('sarana/create', 'SaranaController::create', ['as' => 'sarana.create']);
    $routes->post('sarana/save', 'SaranaController::save', ['as' => 'sarana.save']);
    $routes->get('sarana/detail/(:segment)', 'SaranaController::detail/$1', ['as' => 'sarana.detail']);
    $routes->get('sarana/edit/(:segment)', 'SaranaController::edit/$1', ['as' => 'sarana.edit']);
    $routes->post('sarana/update/(:num)', 'SaranaController::update/$1', ['as' => 'sarana.update']);
    $routes->post('sarana/delete/(:num)', 'SaranaController::delete/$1', ['as' => 'sarana.delete']);

    // sumber data
    $routes->get('sumberdata', 'SumberDataController::index', ['as' => 'sumberdata.index']);
    $routes->post('sumberdata/ajaxList', 'SumberDataController::ajaxList', ['as' => 'sumberdata.ajaxList']);
    // $routes->post('sumberdata/updateData', 'SumberDataController::updateData', ['as' => 'sumberdata.updateData']);
    $routes->post('sumberdata/upload', 'SumberDataController::upload', ['as' => 'sumberdata.upload']);
    $routes->post('sumberdata/delete/(:num)', 'SumberDataController::delete/$1', ['as' => 'sumberdata.delete']);
    $routes->post('sumberdata/deleteAll', 'SumberDataController::deleteAll', ['as' => 'sumberdata.deleteAll']);

    // realisasi
    $routes->get('realisasi', 'RealisasiController::index', ['as' => 'admin.realisasi.index']);
    $routes->post('realisasi/ajaxList', 'RealisasiController::ajaxList', ['as' => 'realisasi.ajaxList']);
    // $routes->get('realisasi/getDataById/(:num)', 'RealisasiController::getDataById/$1', ['as' => 'realisasi.getDataById']);
    // $routes->post('realisasi/updateData', 'RealisasiController::updateData', ['as' => 'realisasi.updateData']);
    $routes->post('realisasi/deleteAll', 'RealisasiController::deleteAll', ['as' => 'realisasi.deleteAll']);
    // realisasi presentase
    $routes->get('presentase/realisasi', 'RealisasiController::indexPresentase', ['as' => 'realisasi.indexPresentase']);
    $routes->get('presentase/realisasi/(:any)', 'RealisasiController::indexPresentase/$1', ['as' => 'realisasi.indexPresentase.filter']);
    $routes->get('presentase/realisasi/(:segment)/(:segment)', 'RealisasiController::indexPresentase/$1/$2', ['as' => 'realisasi.indexPresentase.filter.bulan']);
    // DETAIL REALISASI
    $routes->get('realisasi/detail/(:segment)/(:segment)/(:segment)', 'RealisasiController::getDetailDataByMonth/$1/$2/$3', ['as' => 'realisasi.getDetailDataByMonth']);
    $routes->post('realisasi/ajaxDetailList/(:segment)/(:segment)/(:segment)', 'RealisasiController::ajaxDetailList/$1/$2/$3', ['as' => 'realisasi.ajaxDetailList']);
    $routes->post('realisasi/totalPerKategori', 'RealisasiController::totalPerKategori', ['as' => 'realisasi.totalPerKategori']);

    // target optimasi
    $routes->get('targetoptimasi', 'TargetOptimasiController::index', ['as' => 'targetoptimasi.index']);
    $routes->post('targetoptimasi/ajaxList', 'TargetOptimasiController::ajaxList', ['as' => 'targetoptimasi.ajaxList']);
    $routes->get('targetoptimasi/create', 'TargetOptimasiController::create', ['as' => 'targetoptimasi.create']);
    $routes->post('targetoptimasi/save', 'TargetOptimasiController::save', ['as' => 'targetoptimasi.save']);
    $routes->post('targetoptimasi/savemulti', 'TargetOptimasiController::saveMultiMonthTargetOptimasi', ['as' => 'targetoptimasi.savemulti']);
    $routes->get('targetoptimasi/edit/(:num)', 'TargetOptimasiController::edit/$1', ['as' => 'targetoptimasi.edit']);
    $routes->post('targetoptimasi/update/(:num)', 'TargetOptimasiController::update/$1', ['as' => 'targetoptimasi.update']);
    $routes->post('targetoptimasi/delete/(:num)', 'TargetOptimasiController::delete/$1', ['as' => 'targetoptimasi.delete']);
    $routes->post('targetoptimasi/totalPerKategori', 'TargetOptimasiController::totalPerKategori', ['as' => 'targetoptimasi.totalPerKategori']);


    // target monitoring
    $routes->get('monitoring', 'MonitoringController::index', ['as' => 'admin.monitoring.index']);
    // $routes->get('monitoring/getData', 'MonitoringController::getData', ['as' => 'monitoring.getData']);
    $routes->post('monitoring/getData', 'MonitoringController::getData', ['as' => 'monitoring.getData']);
    $routes->post('monitoring/refresh', 'MonitoringController::refresh', ['as' => 'monitoring.refresh']);
    $routes->get('monitoring/detail/(:num)', 'MonitoringController::detail/$1', ['as' => 'monitoring.detail']);

    // target pagu tersisa
    $routes->get('pagu-tersisa', 'PaguTersisaController::index', ['as' => 'admin.pagutersisa.index']);
    // $routes->get('pagu-tersisa/getData', 'PaguTersisaController::getData', ['as' => 'pagutersisa.getData']);
    $routes->post('pagu-tersisa/getDataWithPercentage', 'PaguTersisaController::getDataWithPercentage', ['as' => 'pagutersisa.getDataWithPercentage']);
    $routes->post('pagu-tersisa/refresh', 'PaguTersisaController::refresh', ['as' => 'pagutersisa.refresh']);
    $routes->get('pagu-tersisa/detail/(:num)', 'PaguTersisaController::detail/$1', ['as' => 'pagutersisa.detail']);
});

// ------------------------------ UNIT WILAYAH ---------------------------------
$routes->group('wilayah', ['filter' => 'authCheck:wilayah'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'wilayah.dashboard']);
    $routes->get('dashboard/top-category-realisasi', 'DashboardController::getTopCategoryRealisasiAjax');

    // categories
    $routes->get('categories', 'CategoriesController::index', ['as' => 'wilayah.categories.index']);
    $routes->post('categories/create', 'CategoriesController::create', ['as' => 'wilayah.categories.create']);
    $routes->post('categories/edit/(:num)', 'CategoriesController::edit/$1', ['as' => 'wilayah.categories.edit']);
    $routes->post('categories/delete/(:num)', 'CategoriesController::delete/$1', ['as' => 'wilayah.categories.delete']);

    // users
    $routes->get('users', 'UsersController::index', ['as' => 'wilayah.users.index']);
    $routes->post('users/create', 'UsersController::create', ['as' => 'wilayah.users.create']);
    $routes->post('users/edit/(:num)', 'UsersController::edit/$1', ['as' => 'wilayah.users.edit']);
    $routes->post('users/delete/(:num)', 'UsersController::delete/$1', ['as' => 'wilayah.users.delete']);
    $routes->get('users/view/(:num)', 'UsersController::view/$1', ['as' => 'wilayah.users.view']);

    // edit profile
    $routes->get('edit_profile', 'UsersController::editProfile', ['as' => 'wilayah.edit_profile']);
    $routes->post('update_profile', 'UsersController::updateProfile', ['as' => 'wilayah.update_profile']);

    // Sumber Data
    $routes->get('sumberdata', 'SumberDataController::index', ['as' => 'wilayah.sumberdata.index']);
    $routes->post('sumberdata/ajaxList', 'SumberDataController::ajaxList', ['as' => 'wilayah.sumberdata.ajaxList']);
    // $routes->post('sumberdata/updateData', 'SumberDataController::updateData', ['as' => 'wilayah.sumberdata.updateData']);
    $routes->post('sumberdata/upload', 'SumberDataController::upload', ['as' => 'wilayah.sumberdata.upload']);
    $routes->post('sumberdata/delete/(:num)', 'SumberDataController::delete/$1', ['as' => 'wilayah.sumberdata.delete']);
    $routes->post('sumberdata/deleteAll', 'SumberDataController::deleteAll', ['as' => 'wilayah.sumberdata.deleteAll']);

    // Realisasi
    $routes->get('realisasi', 'RealisasiController::index', ['as' => 'wilayah.realisasi.index']);
    $routes->post('realisasi/ajaxList', 'RealisasiController::ajaxList', ['as' => 'wilayah.realisasi.ajaxList']);
    // $routes->get('realisasi/getDataById/(:num)', 'RealisasiController::getDataById/$1', ['as' => 'wilayah.realisasi.getDataById']);
    // $routes->post('realisasi/updateData', 'RealisasiController::updateData', ['as' => 'wilayah.realisasi.updateData']);
    $routes->post('realisasi/deleteAll', 'RealisasiController::deleteAll', ['as' => 'wilayah.realisasi.deleteAll']);
    $routes->post('realisasi/totalPerKategori', 'RealisasiController::totalPerKategori', ['as' => 'wilayah.realisasi.totalPerKategori']);
    // realisasi presentase
    $routes->get('presentase/realisasi', 'RealisasiController::indexPresentase', ['as' => 'wilayah.realisasi.indexPresentase']);
    $routes->get('presentase/realisasi/(:any)', 'RealisasiController::indexPresentase/$1', ['as' => 'wilayah.realisasi.indexPresentase.filter']);
    $routes->get('presentase/realisasi/(:segment)/(:segment)', 'RealisasiController::indexPresentase/$1/$2', ['as' => 'wilayah.realisasi.indexPresentase.filter.bulan']);
    // DETAIL REALISASI
    $routes->get('realisasi/detail/(:segment)/(:segment)/(:segment)', 'RealisasiController::getDetailDataByMonth/$1/$2/$3', ['as' => 'wilayah.realisasi.getDetailDataByMonth']);
    $routes->post('realisasi/ajaxDetailList/(:segment)/(:segment)/(:segment)', 'RealisasiController::ajaxDetailList/$1/$2/$3', ['as' => 'wilayah.realisasi.ajaxDetailList']);
    $routes->get('targetoptimasi', 'TargetOptimasiController::index', ['as' => 'wilayah.targetoptimasi.index']);
    $routes->post('targetoptimasi/ajaxList', 'TargetOptimasiController::ajaxList', ['as' => 'wilayah.targetoptimasi.ajaxList']);
    $routes->get('targetoptimasi/create', 'TargetOptimasiController::create', ['as' => 'wilayah.targetoptimasi.create']);
    $routes->post('targetoptimasi/save', 'TargetOptimasiController::save', ['as' => 'wilayah.targetoptimasi.save']);
    $routes->post('targetoptimasi/savemulti', 'TargetOptimasiController::saveMultiMonthTargetOptimasi', ['as' => 'wilayah.targetoptimasi.savemulti']);
    $routes->get('targetoptimasi/edit/(:num)', 'TargetOptimasiController::edit/$1', ['as' => 'wilayah.targetoptimasi.edit']);
    $routes->post('targetoptimasi/update/(:num)', 'TargetOptimasiController::update/$1', ['as' => 'wilayah.targetoptimasi.update']);
    $routes->post('targetoptimasi/delete/(:num)', 'TargetOptimasiController::delete/$1', ['as' => 'wilayah.targetoptimasi.delete']);
    $routes->post('targetoptimasi/totalPerKategori', 'TargetOptimasiController::totalPerKategori', ['as' => 'wilayah.targetoptimasi.totalPerKategori']);

    // Monitoring
    // target monitoring
    $routes->get('monitoring', 'MonitoringController::index', ['as' => 'wilayah.monitoring.index']);
    // $routes->get('monitoring/getData', 'MonitoringController::getData', ['as' => 'wilayah.monitoring.getData']);
    $routes->post('monitoring/getData', 'MonitoringController::getData', ['as' => 'wilayah.monitoring.getData']);

    $routes->post('monitoring/refresh', 'MonitoringController::refresh', ['as' => 'wilayah.monitoring.refresh']);
    $routes->get('monitoring/detail/(:num)', 'MonitoringController::detail/$1', ['as' => 'wilayah.monitoring.detail']);

    // Pagu Tersisa
    $routes->get('pagu-tersisa', 'PaguTersisaController::index', ['as' => 'wilayah.pagutersisa.index']);
    $routes->post('pagu-tersisa/getDataWithPercentage', 'PaguTersisaController::getDataWithPercentage', ['as' => 'wilayah.pagutersisa.getDataWithPercentage']);
    $routes->post('pagu-tersisa/refresh', 'PaguTersisaController::refresh', ['as' => 'wilayah.pagutersisa.refresh']);
    $routes->get('pagu-tersisa/detail/(:num)', 'PaguTersisaController::detail/$1', ['as' => 'wilayah.pagutersisa.detail']);
});

// ------------------------------ UNIT PELAKSANA ---------------------------------
$routes->group('pelaksana', ['filter' => 'authCheck:pelaksana'], function ($routes) {
    $routes->get('dashboard', 'DashboardController::index', ['as' => 'pelaksana.dashboard']);
    $routes->get('dashboard/top-category-realisasi', 'DashboardController::getTopCategoryRealisasiAjax');

    // edit profile
    $routes->get('edit_profile', 'UsersController::editProfile', ['as' => 'pelaksana.edit_profile']);
    $routes->post('update_profile', 'UsersController::updateProfile', ['as' => 'pelaksana.update_profile']);

    // Realisasi
    $routes->get('realisasi', 'RealisasiController::index', ['as' => 'pelaksana.realisasi.index']);
    $routes->post('realisasi/ajaxList', 'RealisasiController::ajaxList', ['as' => 'pelaksana.realisasi.ajaxList']);
    $routes->post('realisasi/totalPerKategori', 'RealisasiController::totalPerKategori', ['as' => 'pelaksana.realisasi.totalPerKategori']);

    // realisasi presentase
    $routes->get('presentase/realisasi', 'RealisasiController::indexPresentase', ['as' => 'pelaksana.realisasi.indexPresentase']);
    $routes->get('presentase/realisasi/(:any)', 'RealisasiController::indexPresentase/$1', ['as' => 'pelaksana.realisasi.indexPresentase.filter']);
    $routes->get('presentase/realisasi/(:segment)/(:segment)', 'RealisasiController::indexPresentase/$1/$2', ['as' => 'pelaksana.realisasi.indexPresentase.filter.bulan']);

    // Target Optimasi
    $routes->get('targetoptimasi', 'TargetOptimasiController::index', ['as' => 'pelaksana.targetoptimasi.index']);
    $routes->post('targetoptimasi/ajaxList', 'TargetOptimasiController::ajaxList', ['as' => 'pelaksana.targetoptimasi.ajaxList']);
    $routes->post('targetoptimasi/totalPerKategori', 'TargetOptimasiController::totalPerKategori', ['as' => 'pelaksana.targetoptimasi.totalPerKategori']);


    // Monitoring
    $routes->get('monitoring', 'MonitoringController::index', ['as' => 'pelaksana.monitoring.index']);
    // $routes->get('monitoring/getData', 'MonitoringController::getData', ['as' => 'pelaksana.monitoring.getData']);
    $routes->post('monitoring/getData', 'MonitoringController::getData', ['as' => 'pelaksana.getData']);

    // DETAIL REALISASI
    $routes->get('realisasi/detail/(:segment)/(:segment)/(:segment)', 'RealisasiController::getDetailDataByMonth/$1/$2/$3', ['as' => 'pelaksana.realisasi.getDetailDataByMonth']);
    $routes->post('realisasi/ajaxDetailList/(:segment)/(:segment)/(:segment)', 'RealisasiController::ajaxDetailList/$1/$2/$3', ['as' => 'pelaksana.realisasi.ajaxDetailList']);
    $routes->get('targetoptimasi', 'TargetOptimasiController::index', ['as' => 'pelaksana.targetoptimasi.index']);
    $routes->post('targetoptimasi/ajaxList', 'TargetOptimasiController::ajaxList', ['as' => 'pelaksana.targetoptimasi.ajaxList']);
    $routes->get('targetoptimasi/create', 'TargetOptimasiController::create', ['as' => 'pelaksana.targetoptimasi.create']);
    $routes->post('targetoptimasi/save', 'TargetOptimasiController::save', ['as' => 'pelaksana.targetoptimasi.save']);
    $routes->get('targetoptimasi/edit/(:num)', 'TargetOptimasiController::edit/$1', ['as' => 'pelaksana.targetoptimasi.edit']);
    $routes->post('targetoptimasi/update/(:num)', 'TargetOptimasiController::update/$1', ['as' => 'pelaksana.targetoptimasi.update']);
    $routes->post('targetoptimasi/delete/(:num)', 'TargetOptimasiController::delete/$1', ['as' => 'pelaksana.targetoptimasi.delete']);

    // Pagu Tersisa
    $routes->get('pagu-tersisa', 'PaguTersisaController::index', ['as' => 'pelaksana.pagutersisa.index']);
    $routes->post('pagu-tersisa/getDataWithPercentage', 'PaguTersisaController::getDataWithPercentage', ['as' => 'pelaksana.pagutersisa.getDataWithPercentage']);
});
