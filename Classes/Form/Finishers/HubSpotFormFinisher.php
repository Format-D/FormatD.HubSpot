<?php

namespace FormatD\HubSpot\Form\Finishers;

use FormatD\HubSpot\Service\HubSpotService;
use Neos\Eel\CompilingEvaluator;
use Neos\Eel\Utility;
use Neos\Form\Core\Model\AbstractFinisher;
use Neos\Flow\Annotations as Flow;

/**
 * Finisher to post form to HubSpot
 */
class HubSpotFormFinisher extends AbstractFinisher {

	/**
	 * @Flow\Inject
	 * @var HubSpotService
	 */
	public HubSpotService $hubSpotService;

	/**
	 * @var array
	 * @Flow\InjectConfiguration(path="formFinisherMappings", package="FormatD.HubSpot")
	 */
	protected array $fieldMappings;

	/**
	 * @Flow\Inject
	 * @var CompilingEvaluator
	 */
	protected $eelEvaluator;

	/**
	 * @return void
	 */
	protected function executeInternal()
	{
		$formFields = $this->collectFormFields();
		$context = $this->collectFormContext();
		$legalConsentOptions = $this->collectFormLegalConsentOptions();
		$this->hubSpotService->submitForm($this->options['formGuid'], $formFields, $context, $legalConsentOptions);
	}

	/**
	 * @return array
	 */
	protected function collectFormFields(): array
	{
		$mappings = $this->fieldMappings[$this->options['mappingSet']];

		$formFields = [];
		foreach ($mappings as $key => $mapping) {

			$eelExpression = $mapping['value'];
			$contextVariables = $this->collectMappingContextVariables($key);

			$formFields[] = [
				'objectTypeId' => $mapping['objectTypeId'],
				"name" => $mapping['name'],
				"value" => Utility::evaluateEelExpression($eelExpression, $this->eelEvaluator, $contextVariables)
			];
		}

		return $formFields;
	}

	/**
	 * @return array
	 */
	protected function collectFormContext(): array
	{
		$httpRequest = $this->finisherContext->getFormRuntime()->getRequest()->getHttpRequest();
		$context = [
			'ipAddress' => $httpRequest->getAttribute('clientIpAddress'),
			'pageUri' => (string) $httpRequest->getUri(),
			//'pageName' => ''
		];
		if (isset($_COOKIE['hubspotutk'])) {
			$context['hutk'] = $_COOKIE['hubspotutk'];
		}
		return $context;
	}

	/**
	 * @return array
	 */
	protected function collectFormLegalConsentOptions(): array
	{
		return [
			'consent' => [
				'consentToProcess' => true,
				'text' => ''
			]
		];
	}

	/**
	 * @param string $localFiledName
	 * @return array
	 */
	protected function collectMappingContextVariables(string $localFiledName) {
		$formValues = $this->finisherContext->getFormValues();
		return [
			'formValues' => $formValues,
			'fieldValue' => isset($formValues[$localFiledName]) ? $formValues[$localFiledName] : null,
		];
	}

}
