<?php

use Doctum\RemoteRepository\GitHubRemoteRepository;
use Doctum\RemoteRepository\GitVersionCollection;

$directory = __DIR__ . '/src';

// $versions = GitVersionCollection::create($directory)
//     ->addFromTags('v0.1.*')
//     ->add('master', 'master branch');

return new \Doctum\Doctum($directory, [
    'title'                => 'Txn Client Library API Documentation',
    'source_dir'           => dirname($directory) . '/',
    'remote_repository'    => new GitHubRemoteRepository('drewlabs/txn-contracts', dirname($directory)),
    'footer_link'          => [
        'href'        => 'https://github.com/azandrew-sidoine/php-txn-client',
        'rel'         => 'noreferrer noopener',
        'target'      => '_blank',
        'before_text' => 'You can edit the configuration',
        'link_text'   => 'on this',
        'after_text'  => 'repository',
    ],
]);
