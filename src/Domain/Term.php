<?php

namespace Hoo\ProductFeeds\Domain;

enum Term: string
{
	case Include = 'include';
	case Exclude = 'exclude';
}