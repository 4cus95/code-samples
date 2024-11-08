<?php


namespace App\Entities\AppleMusic;

use App\Entities\Track as EntityTrack;

class Track extends EntityTrack
{
    public function __construct(string $referenceId, bool $throwError = false)
    {
        $this->provider = config('applemusic.provider');

        parent::__construct($referenceId, $throwError);
    }
}
