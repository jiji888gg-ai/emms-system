<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use App\Models\Student;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
{
    View::composer('student.*', function ($view) {
        $studentId = session('user_id');

        if ($studentId) {
            $student = Student::find($studentId);
            $view->with('student', $student);
        }
    });
}
}
