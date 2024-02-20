<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Auth
Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'Auth\LoginController@login');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

Route::middleware(['auth.timeout', '2fa', 'checkStatus'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::post('/terms-of-use', 'HomeController@acceptTos');
    Route::get('/terms-of-use', 'HomeController@tos')->name('home.tos');

    //Resources
    Route::prefix('resources')->group(function () {
        Route::get('/', 'ResourcesController@index')->name('resources.index');
        Route::get('/create', 'ResourcesController@create', 'create')->name('resources.create');
        Route::post('/create', 'ResourcesController@store', 'store')->name('resources.store');
        Route::get('/edit/{resources}', 'ResourcesController@edit')->name('resources.edit');
        Route::patch('/{resources', 'ResourcesController@update')->name('resources.update');
        Route::get('/delete/{resources}', 'ResourcesController@delete', 'delete')->name('resources.delete');
        Route::get('/download/{resources}', 'ResourcesController@download')->name('resources.download');
    
        Route::middleware('optimizeImages')->group(function () {
            Route::post('/upload', 'ResourcesController@upload')->name('resources.upload');
        });
    });

    // 2fa
    Route::get('/two-factor', 'TwoFAController@index')->name('2fa.index');
    Route::patch('/two-factor/enable', 'TwoFAController@enable')->name('2fa.enable');
    Route::patch('/two-factor/disable', 'TwoFAController@disable')->name('2fa.disable');
    Route::patch('/two-factor/reset/{user}', 'TwoFAController@adminReset')->name('2fa.admin.reset');
    Route::post('/two-factor/verify', 'TwoFAController@verify')->name('2fa.verify');

    // Users
    Route::prefix('users')->group(function () {
        Route::get('/', 'UserController@list')->name('users.list');
        Route::get('/datatables', 'UserController@datatables')->name('users.datatables');
        Route::get('/create', 'UserController@showCreateForm')->name('users.create');
        Route::post('/create', 'UserController@create');
        Route::get('/edit/{id}', 'UserController@showEditForm')->name('users.edit');
        Route::patch('/edit/{id}', 'UserController@edit');
        Route::post('/deactivate', 'UserController@deactivate')->name('users.deactivate');
        Route::post('/reactivate', 'UserController@reactivate')->name('users.reactivate');
        Route::get('/meta/{id}', 'UserController@meta')->name('users.meta');
        Route::post('/signtos', 'UserController@signtos')->name('users.signtos');
        Route::get('/exportLog', 'UserController@exportLog')->name('users.exportLog');
        Route::get('/exportLog/{id}', 'UserController@exportIndividualLog')->name('users.exportIndividualLog');
    });

    // Projects
    Route::prefix('projects')->group(function () {
        Route::get('/', 'ProjectController@list')->name('projects.list');
        Route::get('/datatables', 'ProjectController@datatables')->name('projects.datatables');
        Route::get('/create', 'ProjectController@showCreateForm')->name('projects.create');
        Route::post('/create', 'ProjectController@create');
        Route::get('/edit/{project}', 'ProjectController@showEditForm')->name('projects.edit');
        Route::patch('/edit/{project}', 'ProjectController@edit');
        Route::patch('/addComment/{project}', 'ProjectController@addComment')->name('projects.addComment');
        Route::get('/export', 'ProjectController@export')->name('projects.export');
        Route::get('/exportLog', 'ProjectController@exportLog')->name('projects.exportLog');
        Route::get('/exportLog/{id}', 'ProjectController@exportIndividualLog')->name('projects.exportIndividualLog');
        Route::get('/download/{project}', 'ProjectController@download')->name('projects.download');
        Route::post('/info/{project?}', 'ProjectController@info')->name('projects.info');
        Route::post('/validateBeneficiary', 'ProjectController@validateBeneficiary')->name('projects.validateBeneficiary');
        Route::post('/validateOverseasConference', 'ProjectController@validateOverseasConference')->name('projects.validateOverseasConference');
        Route::get('/donors/{project}', 'ProjectController@donors')->name('projects.donors');
        Route::get('/meta/{project}', 'ProjectController@meta')->name('projects.meta');
        Route::get('/comments/{id}', 'ProjectController@comments')->name('projects.comments');
    });

    // Beneficiaries
    Route::prefix('beneficiaries')->group(function () {
        Route::get('/', 'BeneficiaryController@list')->name('beneficiaries.list');
        Route::get('/datatables', 'BeneficiaryController@datatables')->name('beneficiaries.datatables');
        Route::get('/create', 'BeneficiaryController@showCreateForm')->name('beneficiaries.create');
        Route::post('/create', 'BeneficiaryController@create');
        Route::get('/edit/{id}', 'BeneficiaryController@showEditForm')->name('beneficiaries.edit');
        Route::patch('/edit/{id}', 'BeneficiaryController@edit');
        Route::patch('/addComment/{id}', 'BeneficiaryController@addComment')->name('beneficiaries.addComment');
        Route::post('/delete', 'BeneficiaryController@delete')->name('beneficiaries.delete');
        Route::post('/restore', 'BeneficiaryController@restore')->name('beneficiaries.restore');
        Route::get('/meta/{id}', 'BeneficiaryController@meta')->name('beneficiaries.meta');
        Route::get('/comments/{id}', 'BeneficiaryController@comments')->name('beneficiaries.comments');
        Route::get('/exportLog', 'BeneficiaryController@exportLog')->name('beneficiaries.exportLog');
        Route::get('/exportLog/{id}', 'BeneficiaryController@exportIndividualLog')->name('beneficiaries.exportIndividualLog');
    });

    // Overseas Conferences
    Route::prefix('overseas-conferences')->group(function () {
        Route::get('/', 'OverseasConferenceController@list')->name('overseas-conferences.list');
        Route::get('/datatables', 'OverseasConferenceController@datatables')->name('overseas-conferences.datatables');
        Route::get('/create', 'OverseasConferenceController@showCreateForm')->name('overseas-conferences.create');
        Route::post('/create', 'OverseasConferenceController@create');
        Route::get('/edit/{overseas_conference}', 'OverseasConferenceController@showEditForm')->name('overseas-conferences.edit');
        Route::patch('/edit/{overseas_conference}', 'OverseasConferenceController@edit');
        Route::patch('/addComment/{overseas_conference}', 'OverseasConferenceController@addComment')->name('overseas-conferences.addComment');
        Route::get('/export', 'OverseasConferenceController@export')->name('overseas-conferences.export');
        Route::get('/exportLog', 'OverseasConferenceController@exportLog')->name('overseas-conferences.exportLog');
        Route::get('/exportLog/{id}', 'OverseasConferenceController@exportIndividualLog')->name('overseas-conferences.exportIndividualLog');
        Route::get('/meta/{overseas_conference}', 'OverseasConferenceController@meta')->name('overseas-conferences.meta');
        Route::get('/comments/{id}', 'OverseasConferenceController@comments')->name('overseas-conferences.comments');
    });

    // Local Conferences
    Route::prefix('local-conferences')->group(function () {
        Route::get('/', 'LocalConferenceController@list')->name('local-conferences.list');
        Route::get('/datatables', 'LocalConferenceController@datatables')->name('local-conferences.datatables');
        Route::get('/create', 'LocalConferenceController@showCreateForm')->name('local-conferences.create');
        Route::post('/create', 'LocalConferenceController@create');
        Route::get('/edit/{id}', 'LocalConferenceController@showEditForm')->name('local-conferences.edit');
        Route::patch('/edit/{id}', 'LocalConferenceController@edit');
        Route::patch('/addComment/{id}', 'LocalConferenceController@addComment')->name('local-conferences.addComment');
        Route::get('/export', 'LocalConferenceController@export')->name('local-conferences.export');
        Route::get('/exportLog', 'LocalConferenceController@exportLog')->name('local-conferences.exportLog');
        Route::get('/exportLog/{id}', 'LocalConferenceController@exportIndividualLog')->name('local-conferences.exportIndividualLog');
        Route::get('/meta/{id}', 'LocalConferenceController@meta')->name('local-conferences.meta');
        Route::get('/comments/{id}', 'LocalConferenceController@comments')->name('local-conferences.comments');
    });

    // Twinnings
    Route::prefix('twinnings')->group(function () {
        Route::get('/', 'TwinningController@list')->name('twinnings.list');
        Route::get('/datatables', 'TwinningController@datatables')->name('twinnings.datatables');
        Route::get('/create', 'TwinningController@showCreateForm')->name('twinnings.create');
        Route::post('/create', 'TwinningController@create');
        Route::get('/edit/{twinning}', 'TwinningController@showEditForm')->name('twinnings.edit');
        Route::patch('/edit/{twinning}', 'TwinningController@edit');
        Route::patch('/addComment/{twinning}', 'TwinningController@addComment')->name('twinnings.addComment');
        Route::post('/validateOverseasConference', 'TwinningController@validateOverseasConference')->name('twinnings.validateOverseasConference');
        Route::post('/validateLocalConference', 'TwinningController@validateLocalConference')->name('twinnings.validateLocalConference');
        Route::get('/export', 'TwinningController@export')->name('twinnings.export');
        Route::get('/exportLog', 'TwinningController@exportLog')->name('twinnings.exportLog');
        Route::get('/exportLog/{id}', 'TwinningController@exportIndividualLog')->name('twinnings.exportIndividualLog');
        Route::get('/meta/{twinning}', 'TwinningController@meta')->name('twinnings.meta');
        Route::get('/comments/{id}', 'TwinningController@comments')->name('twinnings.comments');
    });

    // Donors
    Route::prefix('donors')->group(function () {
        Route::get('/', 'DonorController@list')->name('donors.list');
        Route::post('/create', 'DonorController@create')->name('donors.create');
        Route::patch('/edit/{donor}', 'DonorController@edit')->name('donors.edit');
        Route::post('/delete/{donor}', 'DonorController@delete')->name('donors.delete');
    });

    // Contributions
    Route::prefix('contributions')->group(function () {
        Route::post('/create', 'ContributionController@create')->name('contributions.create');
        Route::patch('/edit/{contribution}', 'ContributionController@edit')->name('contributions.edit');
        Route::post('/delete/{project}/{contribution}', 'ContributionController@delete')->name('contributions.delete');
    });

    // Documents
    Route::prefix('documents')->group(function () {
        Route::get('/', 'DocumentController@list')->name('documents.list');
        Route::post('/create', 'DocumentController@create')->name('documents.create');
        Route::patch('/edit/{document}', 'DocumentController@edit')->name('documents.edit');
        Route::post('/delete/{document}', 'DocumentController@delete')->name('documents.delete');
        Route::get('/download/{document}', 'DocumentController@download')->name('documents.download');
        // Route::get('/preview/{document}', 'DocumentController@documentPreview')->name('documents.documentPreview');
    });

    // Activity
    Route::prefix('activity')->group(function () {
        Route::get('/', 'ActivityController@list')->name('activity.list');
        Route::get('/{id}', 'ActivityController@list')->name('activity.id');
    });

    // Remittances
    Route::redirect('/remittances', '/remittances/new', 301);

    Route::prefix('remittances/old')->group(function () {
        Route::get('/', 'OldRemittanceController@list')->name('old-remittances.list');
        Route::get('/datatables', 'OldRemittanceController@datatables')->name('old-remittances.datatables');
        Route::get('/view/{remittance}', 'OldRemittanceController@view')->name('old-remittances.view');
    });

    Route::prefix('remittances/new')->group(function () {
        Route::get('/', 'NewRemittanceController@list')->name('new-remittances.list');
        Route::get('/datatables', 'NewRemittanceController@datatables')->name('new-remittances.datatables');
        Route::get('/create', 'NewRemittanceController@showCreateForm')->name('new-remittances.create');
        Route::post('/create', 'NewRemittanceController@create');
        Route::get('/edit/{remittance}', 'NewRemittanceController@showEditForm')->name('new-remittances.edit');
        Route::get('/approve/{remittance}', 'NewRemittanceController@approve')->name('new-remittances.approve');
        Route::get('/unapprove/{remittance}', 'NewRemittanceController@unapprove')->name('new-remittances.unapprove');
        Route::patch('/edit/{remittance}', 'NewRemittanceController@edit');
        Route::patch('/addComment/{remittance}', 'NewRemittanceController@addComment')->name('new-remittances.addComment');
        Route::get('/donations/{remittance}', 'NewRemittanceController@donations')->name('new-remittances.donations');
        Route::post('/donations/delete/{remittance}', 'NewRemittanceController@delete')->name('new-remittances.delete');
        Route::get('/meta/{remittance}', 'NewRemittanceController@meta')->name('new-remittances.meta');
        Route::get('/download/{remittance}', 'NewRemittanceController@download')->name('new-remittances.download');
        Route::get('/comments/{id}', 'NewRemittanceController@comments')->name('new-remittances.comments');
        Route::get('/exportLog', 'NewRemittanceController@exportLog')->name('new-remittances.exportLog');
        Route::get('/exportLog/{id}', 'NewRemittanceController@exportIndividualLog')->name('new-remittances.exportIndividualLog');
    });

    // Report
    Route::prefix('reports')->group(function () {
        Route::get('/', 'ReportsController@list')->name('reports.list');
        Route::post('/', 'ReportsController@create');
        Route::get('/datatables', 'ReportsController@datatables')->name('reports.datatables');
        Route::get('/download/{year}/{quarter}/{country}', 'ReportsController@download')->name('reports.download');

        Route::get('/yearly', 'ReportsController@yearlyList')->name('reports.yearlyList');
        Route::post('/yearly', 'ReportsController@createYearlyList');
        Route::get('/downloadYearly/{year}', 'ReportsController@downloadYearly')->name('reports.downloadYearly');
        Route::get('/downloaddaterange/{dateStart}/{dateEnd}', 'ReportsController@downloadDateRange')->name('reports.downloadDateRange');
        //Route::get('/download/yearly/{year1}/{year2}', 'ReportsController@downloadYearly')->name('reports.downloadYearly');
    });

    Route::get('/docs/guide', 'HomeController@guide')->name('docs.guide');
});
