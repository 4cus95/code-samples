<?php


namespace App\Services\Settings;


use App\Models\Settings\Settings;
use App\Models\Settings\UserSettings;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserSettingsService
{
    public function getAllUserSettings(User $user): Collection
    {
        return $user->userSettings()->with('settings')->get();
    }

    public function getUserSettingById(User $user, int $id): UserSettings
    {
        return UserSettings::query()
            ->where('user_id', $user->getKey())
            ->findOrFail($id);
    }

    public function toggle(UserSettings $userSetting): UserSettings
    {
        $userSetting->value = !$userSetting->value;
        $userSetting->save();
        $userSetting->refresh();

        return $userSetting->load('settings');
    }

    public function createDefault(User $user): void
    {
        $allSettingsIds = Settings::all()->pluck('id')->toArray();
        $storedSettings = $user->settings->pluck('id')->toArray();

        $needCreateSettings = array_diff($allSettingsIds, $storedSettings);

        $this->createManyByIds($user, $needCreateSettings);
    }

    private function createManyByIds(User $user, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        $settingsToSave = [];

        foreach ($ids as $missingSettingId) {
            $settingsToSave[] = new UserSettings([
                'setting_id' => $missingSettingId,
                'value' => false
            ]);
        }

        $user->userSettings()->saveMany($settingsToSave);
    }
}
