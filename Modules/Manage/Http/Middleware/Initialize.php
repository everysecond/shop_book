<?php namespace Modules\Manage\Http\Middleware;

use Closure;
use Blade;
use Nwidart\Modules\ModulesServiceProvider;
use App\Common\Helpers\Helper;
/**
 * 初始化中间件
 * Class Initialize
 * @package Modules\Manage\Http\Middleware
 */
class Initialize {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        config(['module'=>'manage']);
        /**
         * 注册模板路径
         */
        view()->addLocation(base_path('modules/Manage/Resources/views/'));


        /*
        * 添加快捷标签
        */
        Blade::directive('css', function ($expression) {
            $expression = Helper::parseHtmlTags($expression, 'assets/manage/css/');
            return "<?php echo Html::style{$expression} ?>";
        });
        Blade::directive('js', function ($expression) {
            $expression = Helper::parseHtmlTags($expression, 'assets/manage/js/');
            return "<?php echo Html::script{$expression} ?>";
        });
        Blade::directive('img', function ($expression) {
            $expression = Helper::parseHtmlTags($expression, 'assets/manage/images/');
            return "<?php echo asset($expression) ?>";
        });

        //权限标签
        Blade::directive('per', function ($expression) {
            return "<?php  if (\\App\\Models\\Manager::checkAbility{$expression}) :  ?>";
        });
        Blade::directive('endper', function () {
            return "<?php  endif;  ?>";
        });


        $breadcrumbs = base_path('modules/Manage/Http/Breadcrumbs.php');
        if (file_exists($breadcrumbs)) {
            require $breadcrumbs;
        }

        return $next($request);
    }

}
