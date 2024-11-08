<?php


namespace App\Entities;

use App\Models\Track as ModelTrack;
use App\Models\User;

abstract class Track
{
    protected ModelTrack $track;
    protected ?string $provider;

    public function __construct(string $referenceId, bool $throwError = false)
    {
        $builder = ModelTrack::query()
            ->where('reference_id', $referenceId)
            ->where('provider', $this->provider);

        $this->track = ($throwError ? $builder->firstOrFail() : $builder->first()) ?: new ModelTrack();
    }

    public function getLikesCount(): int
    {
        return $this->track->likes_count ?: 0;
    }

    public function isExists(): bool
    {
        return $this->track->exists ?: false;
    }

    public function getModel(): ModelTrack
    {
        return $this->track;
    }

    public function like(User $user): void
    {
        $track = $this->track;

        if (!$track->likes()->where('user_id', $user->getKey())->exists()) {
            $track->likes()->create(['user_id' => $user->getKey()]);

            $track->increment('likes_count');
        }
    }

    public function unlike(User $user): void
    {
        $track = $this->track;

        if ($track->likes()->where('user_id', $user->getKey())->exists()) {
            $track->likes()->where('user_id', $user->getKey())->delete();

            $track->decrement('likes_count');
        }
    }
}
