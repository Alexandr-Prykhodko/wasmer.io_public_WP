<?php

namespace SEOPressPro\Services\Table;

defined( 'ABSPATH' ) || exit;

use SEOPressPro\Models\Table\TableStructure;
use SEOPressPro\Models\Table\TableColumn;
use SEOPressPro\Models\Table\Table;

class TableList {

    public function getTableSignificantKeywords() {
        $tableStructureImportantKeywords = new TableStructure([
            new TableColumn('id', [
                'type' => 'bigint(20)',
                'primaryKey' => true
            ]),
            new TableColumn('post_id', [
                'type' => 'bigint(20)',
                'index' => true,
                'default' => 0
            ]),
            new TableColumn('word', [
                'type' => 'varchar(100)',
                'index' => true,
                'default' => ''
            ]),
            new TableColumn('count', [
                'type' => 'int(11)',
                'default' => 0
            ]),
            new TableColumn('tf', [
                'type' => 'float',
                'default' => 0.0
            ]),
        ]);

        return new Table("seopress_significant_keywords", $tableStructureImportantKeywords, 1);
    }

    public function getTableSEOIssues() {
        $tableStructure = new TableStructure([
            new TableColumn('id', [
                'type' => 'bigint(20)',
                'primaryKey' => true
            ]),
            new TableColumn('post_id', [
                'type' => 'bigint(20)',
                'index' => true,
                'default' => 0
            ]),
            new TableColumn('issue_name', [
                'type' => 'longtext',
                'default' => ''
            ]),
            new TableColumn('issue_desc', [
                'type' => 'longtext',
                'default' => ''
            ]),
            new TableColumn('issue_type', [
                'type' => 'text',
                'default' => ''
            ]),
            new TableColumn('issue_priority', [
                'type' => 'text',
                'default' => ''
            ]),
            new TableColumn('issue_ignore', [
                'type' => 'boolean',
                'default' => 0
            ]),
        ]);

        return new Table("seopress_seo_issues", $tableStructure, 1);
    }

    public function getTables() {
        return [
            'seopress_significant_keywords' => $this->getTableSignificantKeywords(),
            'seopress_seo_issues' => $this->getTableSEOIssues(),
        ];
    }
}
