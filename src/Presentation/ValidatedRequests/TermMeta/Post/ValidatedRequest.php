<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Presentation\ValidatedRequests\TermMeta\Post;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Http\Request\Validator\ValidatorInterface;

readonly class ValidatedRequest implements RequestInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected ValidatorInterface $validator,
	) {
		($this->validator)([
			Domain\TermMeta::KEY => $this->request->post(Domain\TermMeta::KEY),
		]);
	}

	public function get(string $key): ?string
	{
		return $this->request->get($key);
	}

	public function post(string $key): ?string
	{
		return $this->request->post($key);
	}
}