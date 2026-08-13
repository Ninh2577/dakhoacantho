
use App\Http\Controllers\Api\WpApiController;

Route::prefix(''wp-json/wp/v2'')->middleware(''auth.wp'')->group(function () {
    Route::get(''/users/me'', [WpApiController::class, ''me'']);
    Route::post(''/posts'', [WpApiController::class, ''storePost'']);
});
