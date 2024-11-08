<?php


namespace App\Entities\Factory;

use App\Entities\Track;
use App\Entities\Spotify\Track as SpotifyTrack;
use App\Entities\AppleMusic\Track as AppleTrack;

class TrackFactory
{
    public static function getTrack(string $provider, string $referenceId, bool $throwException = false): Track
    {
        if ($provider == config('spotify.provider')) {
            return new SpotifyTrack($referenceId, $throwException);
        }

        if ($provider == config('applemusic.provider')) {
            return new AppleTrack($referenceId, $throwException);
        }

        throw new \Exception('undefined provider');
    }
}
