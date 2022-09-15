<?php

use Illuminate\Support\Facades\Route;

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



//Route::get('/', function () {return view('welcome');});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', 'HomeController@index')->name('dashboard');

Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');
Route::post('/home', 'HomeController@saveDashboardWidget');
Route::get('/saveWidgetLayout/{type}/{module_id}', 'HomeController@saveWidgetLayout');
Route::get('/defaultDashboard/{type}', 'HomeController@loadDefaultDashboard');


/*PEOPLE ROUTES*/

Route::get('/sites', 'PeopleController@sites');
Route::get('/editSite/{site}', 'PeopleController@editSite');
Route::get('/sites/hazards/{hazard}', 'PeopleController@editHazard');
Route::get('/sites/zone/{zone}', 'PeopleController@editZone');
Route::get('/archiveSite/{site}', 'PeopleController@archiveSite');
Route::get('/copySite/{site}', 'PeopleController@copySite');
Route::get('/completeSite/{site}', 'PeopleController@completeSite');
Route::get('/addSiteRequirement/{permit}/{mandatory}/{site}', 'PeopleController@addSiteRequirement');
Route::get('/addSiteMap/{site}/{map}', 'PeopleController@addSiteMap');
Route::get('/deleteMap/{map}', 'PeopleController@deleteMap');
Route::get('/addSiteZone/{site}/{zone}/{map}', 'PeopleController@addSiteMapZone');
Route::get('/deleteZone/{zone}', 'PeopleController@deleteZone');
Route::get('/addContractorToSite/{site}/{contractor}', 'PeopleController@addContractorToSite');
Route::get('/removeSiteWorker/{site}/{worker}', 'PeopleController@removeWorker');
Route::get('/removeStep/{step}', 'PeopleController@removeStep');
Route::get('/checkZoneHazards/{zone}', 'PeopleController@checkZoneHazards');
Route::get('/addHazardSample/{hazard}/{type}/{date}/{result}', 'PeopleController@addHazardSample');
Route::get('/deleteSample/{sample}', 'PeopleController@deleteSample');
Route::get('/updateMapCoords', 'PeopleController@updateMapCoords');
Route::get('/addZoneRequirement/{zone}/{permit}/{mandatory}', 'PeopleController@AddZoneRequirement');
Route::get('/acceptSite/{site}', 'PeopleController@acceptSite');
Route::get('/removePermit/{type}/{id}', 'PeopleController@removePermit');
Route::get('/removeHazard/{id}', 'PeopleController@removeHazard');


Route::post('/transferControlsOnSite', 'PeopleController@transferControlsOnSite');
Route::post('/saveHazard/{hazard}', 'PeopleController@saveHazard');
Route::post('/addHazard/{zone}', 'PeopleController@addHazard');
Route::post('/saveZone/{zone}', 'PeopleController@saveZone');
Route::post('/saveSite/{site}', 'PeopleController@saveSite');
Route::post('/saveMap/{map}', 'PeopleController@saveMap');
Route::post('/transferMap', 'PeopleController@transferMap');
Route::post('/relinquishSite', 'PeopleController@relinquishSite');
Route::post('/mergeSite', 'PeopleController@mergeSite');


Route::get('/contractors', 'PeopleController@contractors');
Route::get('/editContractor/{contractor}', 'PeopleController@editContractor');
Route::get('/archiveContractor/{contractor}', 'PeopleController@archiveContractor');
Route::post('/saveContractor/{contractor}', 'PeopleController@saveContractor');

Route::get('/builders', 'PeopleController@builders');
Route::get('/editBuilder/{builder}', 'PeopleController@editBuilder');
Route::get('/archiveBuilder/{builder}', 'PeopleController@archiveBuilder');
Route::post('/saveBuilder/{builder}', 'PeopleController@saveBuilder');

Route::get('/users', 'PeopleController@users');
Route::get('/setup/users', 'PeopleController@users');
Route::get('/editProfile/{profile}', 'PeopleController@editProfile');
Route::get('/cancelMembership/{membership}', 'PeopleController@cancelMembership');
Route::get('/archiveProfile/{profile}', 'PeopleController@archiveProfile');
Route::post('/saveProfile/{profile}', 'PeopleController@saveProfile');

Route::get('/hygenists', 'PeopleController@hygenists');
Route::get('/editHygenist/{hygenist}/{type}', 'PeopleController@editHygenist');
Route::get('/archiveHygenist/{hygenist}', 'PeopleController@archiveHygenist');
Route::post('/saveHygenist/{hygenist}', 'PeopleController@saveHygenist');
Route::get('/servicePartners', 'PeopleController@servicePartners');

Route::get('/requestMembership/{hash}/{requestor}', 'PeopleController@requestMembership');
Route::get('/acceptMembership/{membership}/{status}/{profile}', 'PeopleController@acceptMembership');
Route::get('/cancelMembership/{membership}/{profile}', 'PeopleController@cancelMembership');

Route::get('/addTrade/{trade}/{profile}', 'PeopleController@addTrade');
Route::get('/deleteTrade/{trade}', 'PeopleController@deleteTrade');
Route::get('/getPermitTraining/{permit}', 'PeopleController@getPermitTraining');
Route::get('/editPermit/{permit}', 'PeopleController@editPermit');
Route::post('/savePermit/{permit}', 'PeopleController@savePermit');

Route::post('/sendInvitation', 'PeopleController@sendInvitation');




/*EQUIPMENT ROUTES*/

Route::get('/controls', 'EquipmentController@sites');
Route::get('/controls/{site}', 'EquipmentController@controls');
Route::get('/editSiteControl/{site}/{control}', 'EquipmentController@editControl');
Route::get('/logJob/{control}', 'SimproController@controlLogJob');
Route::get('/orderControl/{order}', 'EquipmentController@orderControl');
Route::post('/removeControlsFromSite', 'EquipmentController@removeOrder');
Route::post('/saveOrder/{return}', 'EquipmentController@saveOrder');


/* ACTIVITY ROUTES */


Route::get('/siteHistory', 'ActivityController@sites');
Route::get('/siteHistory/{site}', 'ActivityController@displaySiteHistory');

Route::get('/siteVisit/visit/{site}/{visit}', 'ActivityController@displayHistoryVisit');
Route::get('/siteVisit/person/{person}/{site}', 'ActivityController@displayHistoryPerson');
Route::get('/siteVisit/swms/{swms}', 'ActivityController@editSWMS');
Route::get('/siteVisit/asset/{control}/{site}', 'ActivityController@displayHistoryControl');

Route::get('/logActivity/{history}', 'ActivityController@logActivity');
Route::get('/qrActivity/{site}/{zone}', 'ActivityController@qrActivity'); 
Route::get('/printableQRCode/{zone}', 'ActivityController@printableQRCode');
Route::get('/deleteActivity/{activity}', 'ActivityController@deleteActivity');
Route::get('/deleteHistory/{activity}', 'ActivityController@deleteActivity');

Route::get('/printableSiteQRCode/{site}', 'ActivityController@printableSiteQRCode');
Route::get('/site/logon/{site}', 'PeopleController@siteLogon');
Route::get('/site/logoff', 'PeopleController@siteLogoff');
Route::post('/laterSiteLogoff', 'PeopleController@laterSiteLogoff');

Route::get('/tasks', 'ActivityController@tasks');
Route::get('/editTask/{task}', 'ActivityController@editTask');
Route::get('/completeTask/{task}', 'ActivityController@completeTask');
Route::get('/deleteTask/{task}', 'ActivityController@deleteTask');


Route::get('/training', 'ActivityController@training');
Route::get('/buyTraining/{training}', 'ActivityController@buyTraining');
Route::get('/cancelService/{order}', 'ActivityController@cancelService');
Route::get('/training/join/{training}', 'ActivityController@joinTraining');


Route::get('/getSiteZonesJson/{site}', 'PeopleController@getSiteZonesJson');
Route::get('/getActivityAssessments/{activity}/{id}/{site}/{zone}', 'ActivityController@getActivityAssessments');
Route::get('/requestQuestion/{assessment}/{assessmentQuestion}/{question}/{answer}', 'ActivityController@requestQuestion');
Route::get('/requestPreviousQuestions/{assessment}/{status}', 'ActivityController@requestPreviousQuestions');
Route::get('/submitAssessment/{assessment}', 'ActivityController@submitAssessment');

Route::get('/exposures', 'ActivityController@exposures');
Route::get('/exposurePerson/{person}', 'ActivityController@exposureDetails');
Route::get('/exposureDetail/{person}/{type}', 'ActivityController@exposureTypeDetails');


Route::post('/postSignature/{assessment}', 'ActivityController@postSignature');
Route::post('/saveActivity/{from}', 'ActivityController@saveActivity');
Route::post('/saveTask/{task}', 'ActivityController@saveTask');
Route::post('/buyTraining/{training}', 'ActivityController@confirmTrainingPurchase');
Route::post('/updateMemberPricing/{training}', 'ActivityController@updateMemberPricing');
Route::post('/updateTrainingOrders/{training}', 'ActivityController@updateTrainingOrders');







/* UTILITY ROUTES */

Route::get('/api', 'UtilityController@api');
Route::get('/utilities/import', 'UtilityController@import');
Route::get('/utilities/import/invites', 'UtilityController@importInvites');
Route::post('/utilities/import/invites/process', 'UtilityController@processImportInvites');
Route::get('/utilities/export/', 'UtilityController@export');
Route::get('/account', 'UtilityController@account');
Route::get('/deleteFile/{file}', 'Controller@deleteFile');
Route::get('/updateCostCenters', 'SimproController@retrieveCostCenters');



/* SETUP ROUTES */

Route::get('/setup/trades/{trade}', 'SetupController@trades');
Route::post('/setup/trades', 'SetupController@saveTrade');
Route::get('/setup/trades/archive/{trade}', 'SetupController@archiveTrade');

Route::get('/setup/training', 'SetupController@training');
Route::post('/setup/training', 'SetupController@saveTraining');
Route::get('/setup/training/archive/{training}', 'SetupController@archiveTraining');
Route::get('/setup/trainingTypes/{type}', 'SetupController@trainingTypes');
Route::post('/setup/trainingTypes', 'SetupController@saveTrainingTypes');
Route::get('/setup/trainingTypes/archive/{type}', 'SetupController@archiveTrainingTypes');

Route::get('/setup/permits', 'SetupController@permits');
Route::post('/setup/permits', 'SetupController@savePermit');
Route::get('/setup/permits/archive/{permit}', 'SetupController@archivePermit');

Route::get('/setup/activities', 'SetupController@activities');
Route::post('/setup/activities', 'SetupController@saveActivity');
Route::get('/setup/activities/archive/{activity}', 'SetupController@archiveActivity');

Route::get('/setup/hazards/{hazard}', 'SetupController@hazards');
Route::post('/setup/hazards', 'SetupController@saveHazard');
Route::get('/setup/hazards/archive/{hazard}', 'SetupController@archiveHazard');

Route::get('/setup/samples/{sample}', 'SetupController@samples');
Route::post('/setup/samples', 'SetupController@saveSample');
Route::get('/setup/samples/archive/{sample}', 'SetupController@archiveSample');

Route::get('/setup/news', 'SetupController@news');
Route::post('/setup/news', 'SetupController@saveNews');
Route::get('/setup/news/archive/{news}', 'SetupController@archiveNews');
Route::get('/setup/news/status/{news}', 'SetupController@makeNewsHeadline');

Route::get('/setup/controlTypes', 'SetupController@controlTypes');
Route::get('/setup/controlType/{control}', 'SetupController@editControlType');
Route::post('/setup/controlType', 'SetupController@saveControlType');
Route::get('/setup/controlType/archive/{control}', 'SetupController@archiveControlType');
Route::get('/deleteField/{field}', 'SetupController@deleteField');
Route::post('/editVideo', 'SetupController@saveVideo');
Route::get('/deleteVideo/{video}', 'SetupController@deleteVideo');



Route::get('/editControl/{control}', 'SetupController@editControl');
Route::post('/editControl', 'SetupController@saveControl');
Route::get('/removeControl/{control}', 'SetupController@removeControl');
Route::post('/moveControl/{control}', 'SetupController@moveControl');
Route::get('/archiveControl/{control}', 'SetupController@archiveControl');
Route::get('/addMonitorToControl/{monitor}/{control}', 'SetupController@addMonitor');
Route::get('/removeMonitor/{monitor}', 'SetupController@removeMonitor');


Route::get('/setup/controlGroups/{group}', 'SetupController@controlGroups');
Route::post('/setup/controlGroups', 'SetupController@saveControlGroup');
Route::get('/setup/controlGroups/archive/{group}', 'SetupController@archiveControlGroup');

Route::get('/setup/assessments', 'SetupController@assessments');
Route::get('/setup/assessments/{assessment}', 'SetupController@editAssessment');
Route::post('/setup/saveAssessment/{assessment}', 'SetupController@saveAssessment');
Route::get('/setup/assessments/archive/{assessment}', 'SetupController@archiveAssessment');
Route::get('/getAssessmentGroup/{group}', 'SetupController@getGroup');
Route::get('/getAssessmentQuestion/{question}', 'SetupController@getQuestion');
Route::get('/getQuestionAnswers/{question}', 'SetupController@getAnswers');
Route::get('/getAnswerDetails/{question}', 'SetupController@getAnswerDetails');
Route::get('/getAssessmentQuestions/{assessment}', 'SetupController@getAssessmentQuestions');

Route::get('/setup/securityGroups', 'SetupController@securityGroups');
Route::get('/setup/securityGroup/{type}/{group}', 'SetupController@editSecurityGroup');
Route::get('/setup/archive/securityGroup/{group}', 'SetupController@archiveSecurityGroup');
Route::post('/setup/securityGroup/{type}/{group}', 'SetupController@saveSecurityGroup');

Route::get('/setup/integrations', 'SetupController@integrations');
Route::get('/setup/refreshMonitors', 'ThingsboardController@refreshMonitors');
Route::get('/retrieveMonitors', 'ThingsboardController@retrieveDevices');
Route::get('/setup/monitor/{monitor}', 'SetupController@setupDevice');
Route::get('/getThingsboardGraph/{sensor}/{period}/{readingType}/{site}', 'ThingsboardController@getGraph'); 
Route::get('/getTrafficLight/{sensor}/{period}/{readingType}', 'ThingsboardController@getTrafficLight');

Route::get('/getDeviceReadingTypes/{sensor}', 'ThingsboardController@getReadingTypes');
Route::get('/identifySensor/{sensor}/{reading}', 'ThingsboardController@identifySensor');
Route::get('/getSiteSensors/{site}', 'ThingsboardController@getSiteSensors');


Route::post('/setup/integrations', 'SetupController@saveIntegrations');
Route::post('/setup/device', 'SetupController@saveDevice');
Route::get('/archiveMonitor/{device}', 'SetupController@archiveMonitor');


Route::get('/setup/rules', 'SetupController@rules');
Route::get('/setup/rules/{rule}', 'SetupController@editRule');
Route::get('/setup/rules/archive/{rule}', 'SetupController@archiveRule');
Route::post('/setup/rules', 'SetupController@saveRule');

Route::get('/setup/exposures/{exposure}', 'SetupController@exposures');
Route::post('/setup/exposures', 'SetupController@saveExposure');
Route::get('/setup/exposures/archive/{exposure}', 'SetupController@archiveExposure');


/* SEARCH ROUTES */

Route::post('/search', 'SearchController@results');



/* REPORT ROUTES */

Route::get('/reports/individualExposures', 'ReportController@individualExposures');
Route::get('/reports/billing', 'ReportController@billing');
Route::get('/reports/activities', 'ReportController@activities');
Route::get('/reports/logs', 'ReportController@logs');
Route::get('/reports/controlUsage', 'ReportController@controlUsage');
Route::get('/reports/participation', 'ReportController@participation');
Route::get('/reports/exposure', 'ReportController@exposure');


Route::post('/reports/billing', 'ReportController@filterBilling');
Route::post('/reports/activities', 'ReportController@filterActivities');
Route::post('/reports/logs', 'ReportController@filterLogs');
Route::post('/reports/controlUsage', 'ReportController@filterControlUsage');
Route::post('/reports/participation', 'ReportController@filterParticipation');
Route::post('/reports/exposure', 'ReportController@filterExposure');


/* DASHBOARD ONLY ROUTES */

Route::get('/getMySites', 'ThingsboardController@getMySites');
Route::get('/getBuilderSites', 'ThingsboardController@getBuildersSites');
Route::get('/getMyExposures', 'ThingsboardController@getMyExposures');
Route::get('/getDashboardSettings/{dashboard}', 'HomeController@getSettings');
Route::get('/getSiteParticipation/{site}', 'PeopleController@getSiteParticipation');


/*
    MAIL ROUTES
*/

Route::get('sendemail/{email}','MailController@html_email');
Route::get('sendattachmentemail','MailController@attachment_email');



/* TEST ROUTES */
Route::get('/api/test/{control}/{company}', 'SimproController@lookupAssetTypeDetails');
Route::get('/test3', 'ThingsboardController@testPage');
Route::get('/testEmail', 'ReportController@testEmail');
Route::get('/billingTest/{mode}', 'BillingController@calculate');
Route::get('/fixWidgets', 'HomeController@fixWidgets');
