<?php

namespace FormatD\HubSpot\Eel;

use HubSpot\Discovery\Discovery;
use Neos\Eel\Helper\FileHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;

class HubspotFileHelper extends FileHelper
{
    /**
     * @var array
     * @Flow\InjectConfiguration(path="api", package="FormatD.HubSpot")
     */
    protected array $apiConfiguration;

    /**
     * @var array
     * @Flow\InjectConfiguration(path="fileApi", package="FormatD.HubSpot")
     */
    protected array $fileApiConfiguration;

    /**
     * @var Discovery
     */
    protected Discovery $hubspotApi;

    public function initializeObject(): void {
        $this->hubspotApi = \HubSpot\Factory::createWithAccessToken($this->apiConfiguration['accessToken']);
    }

    public function uploadFileAndGetUrl($resource, ?string $folderId): string
    {
        $folderId = $folderId ?? $this->fileApiConfiguration['folderId'];

        if (!$folderId) {
            return '';
        }

        if(!($resource instanceof PersistentResource)) {
            return '';
        }

        try {
            $fileUri = $resource->createTemporaryLocalCopy();
            $fileObject = new \SplFileObject($fileUri);

            $response = $this->hubspotApi->files()->filesApi()->upload(
                $fileObject,
                $folderId,
                null,
                $resource->getFilename(),
                null,
                json_encode([
                    'access' => 'PUBLIC_NOT_INDEXABLE',     // Private does not work in HS Tickets
                    'ttl' => 'P2W',
                    'overwrite' => false,
                    'duplicateValidationStrategy' => 'NONE',		// do not run any duplicate validation
                    'duplicateValidationScope' => 'EXACT_FOLDER'	// search for a duplicate file in the provided folder
                ])
            );
        } catch (\Exception $exception) {
            \Neos\Flow\var_dump($exception, 'Error during upload');
            return '';
        }

        if ($response instanceof \HubSpot\Client\Files\Model\Error) {
            \Neos\Flow\var_dump($response->getMessage(), 'Error Response');
            return '';
        }

        return $response->getUrl();
    }


    public function getStreamContentAsBase64($resource): string
    {
        if(!($resource instanceof PersistentResource)) {
            return '';
        }

        $streamContents = stream_get_contents($resource->getStream());

        if ($streamContents === false) {
            return '';
        }

        return base64_encode($streamContents);
    }
}
