<?php

namespace FormatD\HubSpot\Service;

use HubSpot\Discovery\Discovery;
use Neos\Flow\Annotations as Flow;
use Psr\Log\LoggerInterface;

/**
 * @Flow\Scope("singleton")
 */
class HubSpotService
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

	/**
	 * @return void
	 */
	public function initializeObject(): void {
		$this->hubspotApi = \HubSpot\Factory::createWithAccessToken($this->apiConfiguration['accessToken']);
	}

	/**
	 * @param string $formGuid
	 * @param array $formFields
	 * @param array $context
	 * @param array $legalConsentOptions
	 * @return \Psr\Http\Message\ResponseInterface
	 */
	public function submitForm(string $formGuid, array $formFields, array $context, array $legalConsentOptions): \Psr\Http\Message\ResponseInterface {

		$requestOptions = [
			'method' => 'POST',
			'baseUrl' => 'https://api.hsforms.com',
			'path' => '/submissions/v3/integration/submit/' . $this->apiConfiguration['portalId'] . '/' . $formGuid,
			'body' => [
				"fields" => $formFields,
				"context" => $context,
				"legalConsentOptions" => $legalConsentOptions,
			],
		];

        $response = null;

		try {
			$response = $this->hubspotApi->apiRequest($requestOptions);
		} catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->logger->error('Error during form submit to HubSpot: '. $e->getMessage());
		}

		return $response;
	}

}
