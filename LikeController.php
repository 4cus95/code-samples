<?php

namespace App\Http\Controllers\Streaming;

use App\Entities\Factory\TrackFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Like\LikeRequest;
use App\Services\Media\AlbumService;
use App\Services\Media\ArtistService;
use App\Services\Media\PlaylistService;
use App\Services\Media\TrackService;
use App\Services\Streaming\ParserService;
use App\Services\Streaming\StreamService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    use ApiResponder;

    public function __construct(
        protected TrackService $trackService,
        protected StreamService $streamService,
        protected ParserService $parserService,
        protected AlbumService $albumService,
        protected ArtistService $artistService,
        protected PlaylistService $playlistService,
    ) {

    }

    public function likeTrack(LikeRequest $request, $referenceId): JsonResponse
    {
        $provider = $request->validated('provider');
        $user = $request->user();

        $track = TrackFactory::getTrack($provider, $referenceId);

        if (!$track->isExists()) {
            $trackRawData = $this->streamService->getReference($request->user(), $provider, $referenceId, 'track');
            $structuredData = $this->parserService->convertStreamingResponse(
                $request->user(),
                $trackRawData,
                $provider,
                'track'
            );
            $track = $this->trackService->save($structuredData);
        }

        $track->like($user);

        return $this->successResponse(__('messages.liked'));
    }

    public function unlikeTrack(LikeRequest $request, $referenceId): JsonResponse
    {
        $provider = $request->validated('provider');

        $track = TrackFactory::getTrack($provider, $referenceId, true);

        $track->unlike($request->user());

        return $this->successResponse(__('messages.unliked'));
    }
}
