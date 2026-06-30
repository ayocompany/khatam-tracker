<?php


namespace App\Providers;

use App\Repositories\Contracts\KhatamDailyLogRepositoryInterface;
use App\Repositories\Contracts\KhatamHistoryRepositoryInterface;
use App\Repositories\Contracts\KhatamProgramRepositoryInterface;
use App\Repositories\Contracts\KhatamProgressRepositoryInterface;
use App\Repositories\Contracts\PageMappingRepositoryInterface;
use App\Repositories\Contracts\QuranSurahRepositoryInterface;
use App\Repositories\Contracts\QuranVerseRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\WhatsappOtpRepositoryInterface;
use App\Repositories\Eloquent\KhatamDailyLogRepository;
use App\Repositories\Eloquent\KhatamHistoryRepository;
use App\Repositories\Eloquent\KhatamProgramRepository;
use App\Repositories\Eloquent\KhatamProgressRepository;
use App\Repositories\Eloquent\PageMappingRepository;
use App\Repositories\Eloquent\QuranSurahRepository;
use App\Repositories\Eloquent\QuranVerseRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\WhatsappOtpRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(QuranSurahRepositoryInterface::class, QuranSurahRepository::class);
        $this->app->bind(QuranVerseRepositoryInterface::class, QuranVerseRepository::class);
        $this->app->bind(PageMappingRepositoryInterface::class, PageMappingRepository::class);
        $this->app->bind(KhatamDailyLogRepositoryInterface::class, KhatamDailyLogRepository::class);
        $this->app->bind(KhatamHistoryRepositoryInterface::class, KhatamHistoryRepository::class);
        $this->app->bind(KhatamProgramRepositoryInterface::class, KhatamProgramRepository::class);
        $this->app->bind(KhatamProgressRepositoryInterface::class, KhatamProgressRepository::class);
        $this->app->bind(WhatsappOtpRepositoryInterface::class, WhatsappOtpRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
    }
}
