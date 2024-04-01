<?php

use App\Http\Controllers\Api\Profile\LiveController;
use App\Http\Controllers\Api\Profile\AdvertiseController;
use App\Http\Controllers\Api\Profile\MatchController;
use App\Http\Controllers\Api\Profile\ClubController;
use App\Http\Controllers\Api\Profile\CountryController;
use App\Http\Controllers\Api\Profile\LeagueController;
use App\Http\Controllers\Api\Profile\PageController;
use App\Http\Controllers\Api\Profile\PostController;
use App\Http\Controllers\Api\Profile\ReportController;
use App\Http\Controllers\Api\Profile\RoleController;
use App\Http\Controllers\Api\Profile\RpcController;
use App\Http\Controllers\Api\Profile\SportController;
use App\Http\Controllers\Api\Profile\StatusController;
use App\Http\Controllers\Api\Profile\StepController;
use App\Http\Controllers\Api\Profile\TicketController;
use App\Http\Controllers\Api\Profile\TicketSubjectController;
use App\Http\Controllers\Api\Profile\UserController;
use App\Http\Controllers\Api\Profile\VideoController;
use App\Http\Controllers\Api\Social\ChatController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum', 'auth'])->prefix('profile')->group(function() {

    // Posts
    Route::prefix('posts')->group(function () {
        Route::get('/{post}', [PostController::class, 'show'])->name('profile.post.show')->middleware('permission:post_show');
        Route::get('/', [PostController::class, 'index'])->name('profile.post.index')->middleware('permission:post_show');
        Route::post('/', [PostController::class, 'store'])->name('profile.post.store')->middleware('permission:post_store');
        Route::post('/{post}', [PostController::class, 'update'])->name('profile.post.update')->middleware('permission:post_update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('profile.post.delete')->middleware('permission:post_delete');
        Route::delete('/delete/{id}', [PostController::class, 'realDestroy'])->name('profile.post.real.delete')->middleware('permission:post_delete');
    });

    // video
    Route::prefix('videos')->group(function () {
        Route::get('/index', [VideoController::class, 'index'])->name('profile.video.index')->middleware('permission:video_show');
        Route::get('/{video}', [VideoController::class, 'show'])->name('profile.video.show')->middleware('permission:video_show');
        Route::get('/', [VideoController::class, 'indexPaginate'])->name('profile.video.paginate')->middleware('permission:video_show');
        Route::post('/', [VideoController::class, 'store'])->name('profile.video.store')->middleware('permission:video_store');
        Route::post('/{video}', [VideoController::class, 'update'])->name('profile.video.update')->middleware('permission:video_update');
        Route::delete('/{video}', [VideoController::class, 'destroy'])->name('profile.video.delete')->middleware('permission:video_delete');
    });

    // Status
    Route::prefix('status')->group(function () {
        Route::get('/', [StatusController::class, 'index'])->name('profile.status.index')->middleware('permission:status_show');
        Route::post('/', [StatusController::class, 'store'])->name('profile.status.store')->middleware('permission:status_store');
        Route::post('/{status}', [StatusController::class, 'update'])->name('profile.status.update')->middleware('permission:status_update');
        Route::delete('/{status}', [StatusController::class, 'destroy'])->name('profile.status.delete')->middleware('permission:status_delete');
        Route::delete('/delete/{status}', [StatusController::class, 'realDestroy'])->name('profile.status.real.delete')->middleware('permission:status_delete');
    });

    // userInfo
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'indexPaginate'])->name('profile.users.paginate')->middleware('permission:user_show');
        Route::get('/info/{user?}', [UserController::class, 'show'])->name('profile.user.show')->middleware('permission:user_show');
        Route::post('/', [UserController::class, 'store'])->name('profile.user.store')->middleware('permission:user_store');
        Route::post('/{user}', [UserController::class, 'update'])->name('profile.user.update')->middleware('permission:user_update');
        Route::patch('/password', [UserController::class, 'updatePassword'])->name('profile.user.password.update')->middleware('permission:user_update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('profile.user.delete')->middleware('permission:user_delete');
    });

    // sports
    Route::prefix('sports')->group(function () {
        Route::get('/', [SportController::class, 'indexPaginate'])->name('profile.sport')->middleware('permission:sport_show');
        Route::get('/{sport}', [SportController::class, 'show'])->name('profile.sport.show')->middleware('permission:sport_show');
        Route::post('/', [SportController::class, 'store'])->name('profile.sport.store')->middleware('permission:sport_store');
        Route::post('/{sport}', [SportController::class, 'update'])->name('profile.sport.update')->middleware('permission:sport_update');
        Route::delete('/{sport}', [SportController::class, 'destroy'])->name('profile.sport.delete')->middleware('permission:sport_delete');
    });

    // countries
    Route::prefix('countries')->group(function () {
        Route::get('/', [CountryController::class, 'indexPaginate'])->name('profile.country.index')->middleware('permission:country_show');
        Route::get('/{country}', [CountryController::class, 'show'])->name('profile.country.show')->middleware('permission:country_show');
        Route::post('/', [CountryController::class, 'store'])->name('profile.country.store')->middleware('permission:country_store');
        Route::post('/{country}', [CountryController::class, 'update'])->name('profile.country.update')->middleware('permission:country_update');
        Route::delete('/{country}', [CountryController::class, 'destroy'])->name('profile.country.delete')->middleware('permission:country_delete');
    });

    // lives
    Route::prefix('lives')->group(function () {
        Route::get('/index', [LiveController::class, 'index'])->name('profile.live')->middleware('permission:live_show');
        Route::get('/', [LiveController::class, 'indexPaginate'])->name('profile.live.index')->middleware('permission:live_show');
        Route::get('/{live}', [LiveController::class, 'show'])->name('profile.live.show')->middleware('permission:live_show');
        Route::post('/', [LiveController::class, 'store'])->name('profile.live.store')->middleware('permission:live_store');
        Route::post('/{live}', [LiveController::class, 'update'])->name('profile.live.update')->middleware('permission:live_update');
        Route::delete('/{live}', [LiveController::class, 'destroy'])->name('profile.live.delete')->middleware('permission:live_delete');
    });

    // lives
    Route::prefix('reports')->group(function () {
        // Route::get('/index', [LiveController::class, 'index'])->name('profile.live')->middleware('permission:live_show');
        Route::get('/', [ReportController::class, 'indexPaginate'])->name('profile.report.index')->middleware('permission:report_show');
        Route::get('/{report}', [ReportController::class, 'show'])->name('profile.report.show')->middleware('permission:report_show');
        Route::post('/', [ReportController::class, 'store'])->name('profile.report.store')->middleware('permission:report_store');
        Route::post('/{report}', [ReportController::class, 'close'])->name('profile.report.close')->middleware('permission:report_update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('profile.report.delete')->middleware('permission:report_delete');
    });

    // clubs
    Route::prefix('clubs')->group(function () {
        Route::get('/all/{sport?}/{country?}', [ClubController::class, 'index'])->name('profile.club.all')->middleware('permission:club_show');
        Route::get('/', [ClubController::class, 'indexPaginate'])->name('profile.club.index')->middleware('permission:club_show');
        Route::get('/active/{club}', [ClubController::class, 'isActive'])->name('profile.club.active');
        Route::get('/{club}', [ClubController::class, 'show'])->name('profile.club.show')->middleware('permission:club_show');
        Route::post('/', [ClubController::class, 'store'])->name('profile.club.store')->middleware('permission:club_store');
        Route::post('/{club}', [ClubController::class, 'update'])->name('profile.club.update')->middleware('permission:club_update');
        Route::delete('/{club}', [ClubController::class, 'destroy'])->name('profile.club.delete')->middleware('permission:club_delete');
    });

    // leagues
    Route::prefix('leagues')->group(function () {
        Route::get('/', [LeagueController::class, 'indexPaginate'])->name('profile.league.index')->middleware('permission:league_show');
        Route::get('/{league}', [LeagueController::class, 'show'])->name('profile.league.show')->middleware('permission:league_show');
        Route::post('/', [LeagueController::class, 'store'])->name('profile.league.store')->middleware('permission:league_store');
        Route::post('/{league}', [LeagueController::class, 'update'])->name('profile.league.update')->middleware('permission:league_update');
        Route::delete('/{league}', [LeagueController::class, 'destroy'])->name('profile.league.delete')->middleware('permission:league_delete');
        Route::get('/{league}/clubs', [LeagueController::class, 'getClubs'])->name('profile.league.clubs')->middleware('permission:league_show');
        Route::post('/{league}/clubs', [LeagueController::class, 'storeClubs'])->name('profile.league.clubs.store')->middleware('permission:league_store');
        Route::get('/{league}/steps', [LeagueController::class, 'getAllSteps'])->name('profile.league.steps')->middleware('permission:league_show');
        Route::post('/{league}/steps/', [StepController::class, 'store'])->name('profile.step.store')->middleware('permission:league_store');
        Route::post('/{league}/steps/{step}', [StepController::class, 'update'])->name('profile.step.update')->middleware('permission:league_update');
    });

    // steps
    Route::prefix('steps')->group(function () {
        Route::get('/{step}', [StepController::class, 'show'])->name('profile.step.show')->middleware('permission:step_show');
        Route::get('/{step}/info', [StepController::class, 'getStepInfo'])->name('profile.step.info')->middleware('permission:step_show');
        Route::get('/create/{league}', [StepController::class, 'create'])->name('profile.step.create')->middleware('permission:step_show');
        Route::delete('/{step}', [StepController::class, 'destroy'])->name('profile.step.destroy')->middleware('permission:step_delete');
        Route::get('/{step}/clubs', [StepController::class, 'getAllClubs'])->name('profile.step.clubs')->middleware('permission:step_show');
        Route::post('/{step}/clubs', [StepController::class, 'storeClubs'])->name('profile.step.clubs.store')->middleware('permission:step_store');
        Route::get('/{step}/matches', [StepController::class, 'getAllMatches'])->name('profile.step.matches')->middleware('permission:match_show');
        Route::post('/{step}/matches', [MatchController::class, 'store'])->name('profile.match.store')->middleware('permission:match_store');
        Route::post('/{step}/matches/{matches}', [MatchController::class, 'update'])->name('profile.match.update')->middleware('permission:match_update');

    });

    // matches
    Route::prefix('matches')->group(function () {
        Route::get('/{matches}', [MatchController::class, 'show'])->name('profile.match.show')->middleware('permission:match_show');
        // Route::patch('/{matches}', [MatchController::class, 'update'])->name('profile.match.update');
        Route::delete('/{matches}', [MatchController::class, 'destroy'])->name('profile.match.destroy')->middleware('permission:match_delete');
        // Route::post('/{step}', [MatchController::class, 'store'])->name('profile.match.store');
    });

     // advertises
     Route::prefix('advertise')->group(function () {
        Route::get('/places', [AdvertiseController::class, 'getPlaces'])->name('profile.advertise.places')->middleware('permission:advertise_show');
        Route::get('/', [AdvertiseController::class, 'indexPaginate'])->name('profile.advertise.index')->middleware('permission:advertise_show');
        Route::get('/{advertise}', [AdvertiseController::class, 'show'])->name('profile.advertise.show')->middleware('permission:advertise_show');
        Route::post('/', [AdvertiseController::class, 'store'])->name('profile.advertise.store')->middleware('permission:advertise_store');
        Route::post('/{advertise}', [AdvertiseController::class, 'update'])->name('profile.advertise.update')->middleware('permission:advertise_update');
        Route::delete('/{advertise}', [AdvertiseController::class, 'destroy'])->name('profile.advertise.delete')->middleware('permission:advertise_delete');
    });

     // advertise form
     Route::prefix('advertise-form')->group(function () {
        Route::get('/', [AdvertiseController::class, 'indexFormPaginate'])->name('profile.advertise-form.index')->middleware('permission:advertise_show');
        Route::delete('/{advertiseForm}', [AdvertiseController::class, 'destroyForm'])->name('profile.advertise-form.delete')->middleware('permission:advertise_delete');
    });


     // roles
     Route::prefix('role')->group(function () {
        Route::get('/', [RoleController::class, 'roles'])->name('profile.roles.index')->middleware('permission:role_show');
        Route::get('/permissions', [RoleController::class, 'permissions'])->name('profile.permissions.index')->middleware('permission:permission_show');
    });

    // pages
    Route::prefix('pages')->group(function () {
        Route::get('/', [PageController::class, 'indexPaginate'])->name('profile.page.index')->middleware('permission:page_show');
        Route::get('/{page}', [PageController::class, 'show'])->name('profile.page.show')->middleware('permission:page_show');
        Route::post('/', [PageController::class, 'store'])->name('profile.page.store')->middleware('permission:page_store');
        Route::post('/{page}', [PageController::class, 'update'])->name('profile.page.update')->middleware('permission:page_update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('profile.page.delete')->middleware('permission:page_delete');
    });

    // notifications
    // Route::prefix('notifications')->group(function () {
    //     Route::get('/{user?}', [NotificationController::class, 'indexPaginate'])->name('profile.page.index')->middleware('permission:notification_show');
    // });

    // rpc
    Route::prefix('rpc')->group(function () {
        Route::get('/', [RpcController::class, 'index'])->name('profile.rpc.index');
    });

    // chats
    Route::prefix('chats')->group(function () {
        Route::post('/', [ChatController::class, 'indexPaginate'])->name('profile.chat.index')->middleware('permission:chat_show');
        Route::get('/{chat}', [ChatController::class, 'show'])->name('profile.chat.show')->middleware('permission:chat_show');
        Route::get('/info/{chat}', [ChatController::class, 'chatInfo'])->name('profile.chat.info')->middleware('permission:chat_show');
        Route::post('/{user}', [ChatController::class, 'store'])->name('profile.chat.store')->middleware('permission:chat_store');
        Route::post('delete/{chat}', [ChatController::class, 'deleteMessages'])->name('profile.chat.delete.messages')->middleware('permission:chat_delete');
        // Route::post('/status/{chat}', [ChatController::class, 'changeStatus'])->name('profile.chat.change-status')->middleware('permission:chat_store');
    });

    // tickets
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'indexPaginate'])->name('profile.ticket.index')->middleware('permission:ticket_show');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('profile.ticket.show')->middleware('permission:ticket_show');
        Route::post('/', [TicketController::class, 'store'])->name('profile.ticket.store')->middleware('permission:ticket_store');
        Route::post('/{ticket}', [TicketController::class, 'storeMessage'])->name('profile.ticket.store.message')->middleware('permission:ticket_store');
        Route::post('/status/{ticket}', [TicketController::class, 'changeStatus'])->name('profile.ticket.change-status')->middleware('permission:ticket_store');
    });

    // ticket subjects
    Route::prefix('ticket-subjects')->group(function () {
        Route::get('/', [TicketSubjectController::class, 'indexPaginate'])->name('profile.ticket-subject.index')->middleware('permission:subject_show');
        Route::get('/{ticketSubject}', [TicketSubjectController::class, 'show'])->name('profile.ticket-subject.show')->middleware('permission:subject_show');
        Route::post('/', [TicketSubjectController::class, 'store'])->name('profile.ticket-subject.store')->middleware('permission:subject_store');
        Route::post('/{ticketSubject}', [TicketSubjectController::class, 'update'])->name('profile.ticket-subject.update')->middleware('permission:subject_update');
        Route::delete('/{ticketSubject}', [TicketSubjectController::class, 'destroy'])->name('profile.ticket-subject.delete')->middleware('permission:subject_delete');
    });

});
