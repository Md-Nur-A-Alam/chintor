<?php

namespace App\Services\Media;

use App\Models\Media;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

/**
 * Uploads images to ImgBB (https://api.imgbb.com). A temporary "plan B"
 * until Cloudflare R2 is set up.
 */
class ImgbbImageStore implements ImageStore
{
    public const ENDPOINT = 'https://api.imgbb.com/1/upload';

    /**
     * A 1×1 transparent PNG used to verify the API key.
     */
    private const CHECK_PIXEL = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    public function __construct(private readonly string $apiKey) {}

    public function provider(): string
    {
        return Media::ProviderImgbb;
    }

    public function store(UploadedFile $file, string $directory): StoredImage
    {
        $response = $this->upload(
            contents: (string) file_get_contents($file->getRealPath()),
            filename: $file->hashName(),
            name: $directory.'-'.pathinfo($file->hashName(), PATHINFO_FILENAME),
        );

        return new StoredImage(
            provider: $this->provider(),
            url: $response->json('data.url'),
            path: $response->json('data.id'),
            deleteUrl: $response->json('data.delete_url'),
        );
    }

    /**
     * ImgBB's API cannot delete images; the record is removed here and the
     * image can be deleted by hand from its `delete_url` if needed.
     */
    public function delete(Media $media): void {}

    public function checkConnection(): void
    {
        // The test pixel deletes itself after 60 seconds.
        $this->upload(
            contents: (string) base64_decode(self::CHECK_PIXEL),
            filename: 'chintor-check.png',
            name: 'chintor-connection-check',
            expirationSeconds: 60,
        );
    }

    /**
     * Fields go in the multipart body (as in ImgBB's docs) with an honest,
     * descriptive User-Agent: ImgBB's firewall rejects some generic HTTP
     * client agents with "You have been forbidden to use this website."
     */
    private function upload(string $contents, string $filename, string $name, ?int $expirationSeconds = null): Response
    {
        $fields = array_filter([
            'key' => $this->apiKey,
            'name' => $name,
            'expiration' => $expirationSeconds,
        ]);

        try {
            $response = Http::timeout(30)
                ->withUserAgent('Mozilla/5.0 (compatible; ChintorMedia/1.0; +'.config('app.url').')')
                ->acceptJson()
                ->attach('image', $contents, $filename)
                ->post(self::ENDPOINT, $fields);
        } catch (ConnectionException) {
            throw new MediaStorageException('ImgBB সার্ভারের সাথে সংযোগ করা যায়নি। ইন্টারনেট সংযোগ দেখুন।');
        }

        if ($response->failed() || $response->json('success') !== true || blank($response->json('data.url'))) {
            throw new MediaStorageException($this->failureMessage($response));
        }

        return $response;
    }

    private function failureMessage(Response $response): string
    {
        $reason = $response->json('error.message') ?? "HTTP {$response->status()}";

        if ($response->status() === 403 || str_contains((string) $reason, 'forbidden')) {
            return "ImgBB আপলোড ব্যর্থ (HTTP {$response->status()}): {$reason} — ImgBB আপনার নেটওয়ার্ক/IP ব্লক করেছে। "
                .'VPN বন্ধ করে বা অন্য ইন্টারনেট সংযোগ থেকে চেষ্টা করুন; লাইভ সার্ভার থেকে সাধারণত কাজ করে। '
                .'ততক্ষণ “স্বয়ংক্রিয়” মোডে ছবি এই সার্ভারে সেভ হবে।';
        }

        return "ImgBB আপলোড ব্যর্থ (HTTP {$response->status()}): {$reason}";
    }
}
