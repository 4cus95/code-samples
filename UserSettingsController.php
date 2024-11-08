<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Services\Settings\UserSettingsService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Settings\UserSettingsRequest;

class UserSettingsController extends Controller
{
    use ApiResponder;

    protected UserSettingsService $userSettingsService;

    public function __construct(UserSettingsService $userSettingsService)
    {
        $this->userSettingsService = $userSettingsService;
    }

    public function update(UserSettingsRequest $request, int $setting_id): JsonResponse
    {
        $userSetting = $this->userSettingsService->getUserSettingById($request->user(), $setting_id);

        $userSettingUpdated = $this->userSettingsService->toggle($userSetting);

        return $this->dataResponse($userSettingUpdated);
    }

    public function index(UserSettingsRequest $request): JsonResponse
    {
        $userSettings = $this->userSettingsService->getAllUserSettings($request->user());

        return $this->dataResponse($userSettings);
    }
}
