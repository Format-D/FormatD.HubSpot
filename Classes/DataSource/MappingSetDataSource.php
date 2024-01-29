<?php
namespace FormatD\HubSpot\DataSource;

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Neos\Service\DataSource\AbstractDataSource;

class MappingSetDataSource extends AbstractDataSource {

	/**
	 * @var string
	 */
	static protected $identifier = 'formatd-hubspot-mapping-sets';

	/**
	 * @var array
	 * @Flow\InjectConfiguration(path="formFinisherMappings", package="FormatD.HubSpot")
	 */
	protected array $fieldMappings;

	/**
	 * Get data
	 *
	 * The return value must be JSON serializable data structure.
	 *
	 * @param NodeInterface $node The node that is currently edited (optional)
	 * @param array $arguments Additional arguments (key / value)
	 * @return mixed JSON serializable data
	 * @api
	 */
	public function getData(NodeInterface $node = null, array $arguments = []): array {
		$data = [];
		foreach ($this->fieldMappings as $key => $mapping)
		{
			$data[$key] = ['label' => $key, 'value' => $key];
		}
        ksort($data);
		return $data;
	}

}
