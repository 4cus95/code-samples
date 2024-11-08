<?php


namespace App\Entities\Spotify;

use App\Entities\Track as EntityTrack;

class Track extends EntityTrack
{
    public function __construct(string $referenceId, bool $throwError = false)
    {
        $this->provider = config('spotify.provider');

        parent::__construct($referenceId, $throwError);
    }
}
