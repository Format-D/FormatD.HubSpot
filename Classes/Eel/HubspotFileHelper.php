<?php

namespace FormatD\HubSpot\Eel;

use HubSpot\Discovery\Discovery;
use Neos\Eel\Helper\FileHelper;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\ResourceManagement\PersistentResource;
use Psr\Log\LoggerInterface;

class HubspotFileHelper extends FileHelper
{
    /**
     * @var array
     * @Flow\InjectConfiguration(path="api", package="FormatD.HubSpot")
     */
    protected array $apiConfiguration;

    /**
     * @var Discovery
     */
    protected Discovery $hubspotApi;

    /**
     * @Flow\Inject
     * @var LoggerInterface
     */
    protected $logger;

    public function initializeObject(): void {
        $this->hubspotApi = \HubSpot\Factory::createWithAccessToken($this->apiConfiguration['accessToken']);
    }

    public function uploadFileAndGetUrl($resource, ?string $folderId = null): string
    {
        $folderId = $folderId ?? $this->apiConfiguration['defaultUploadFolderId'];

        if (!$folderId) {
            $this->logger->warning('No folderId specified for uploading files to Hubspot. Returning empty URL for file upload field.');
            return '';
        }

        if(!($resource instanceof PersistentResource)) {
            $type = gettype($resource);
            $type = $type === 'object' ? get_class($resource) : $type;
            $this->logger->warning('Resource is not a PersistentResource, but of type ' . $type . '. Returning empty URL for file upload field.');
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
            $this->logger->error('Error during file uploading to Hubspot: ' . $exception->getMessage() . '. Returning empty URL for file upload field.');
            return '';
        }

        if ($response instanceof \HubSpot\Client\Files\Model\Error) {
            $this->logger->error('Error during file uploading to Hubspot: ' . $response->getMessage() . '. Returning empty URL for file upload field.');
            return '';
        }

        return $response->getUrl();
    }
}
