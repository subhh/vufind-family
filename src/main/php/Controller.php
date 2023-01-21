<?php

/*
 * This file is part of VuFind Family.
 *
 * VuFind Family is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or (at your
 * option) any later version.
 *
 * VuFind Family is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with VuFind Family. If not, see <https://www.gnu.org/licenses/>.
 *
 * @author    David Maus <david.maus@sub.uni-hamburg.de>
 * @copyright (c) 2023 by Staats- und Universitätsbibliothek Hamburg
 * @license   http://www.gnu.org/licenses/gpl.txt GNU General Public License v3 or higher
 */

namespace SUBHH\VuFind\Family;

use UnexpectedValueException;

use VuFind\AjaxHandler\AbstractBase;
use Laminas\Mvc\Controller\Plugin\Params;

use VuFindSearch\Backend\Solr\Command\RawJsonSearchCommand;
use VuFindSearch\Service;
use VuFindSearch\ParamBag;
use VuFindSearch\Query\Query;

use StdClass;

final class Controller extends AbstractBase
{
    /** @var Service */
    private $search;

    public function __construct (Service $search)
    {
        $this->search = $search;
    }

    /** @return array<mixed> */
    public function handleRequest (Params $params) : array
    {
        $identifier = $params->fromQuery('identifier');
        if (!$identifier) {
            return ["The required parameter 'identifier' is missing", 400];
        }
        $source = $params->fromQuery('source', 'solr');
        $query = new Query();
        $params = new ParamBag();
        $params->add('fl', 'id');
        $params->add('fl', 'title_full');
        $params->add('fl', 'publishDate');
        $params->set('fq', sprintf('!id:%s', $identifier));
        $params->add('fq', sprintf('hierarchy_top_id:%s', $identifier));
        $command = new RawJsonSearchCommand($source, $query, 0, 20, $params);
        $this->search->invoke($command);
        $result = $command->getResult();

        if (!$result instanceof StdClass) {
            throw new UnexpectedValueException("Unexpected response from '{$source}' backend");
        }

        $records = array();
        if (isset($result->response->numFound)) {
            $numFound = $result->response->numFound;
        } else {
            $numFound = 0;
        }
        if ($numFound < 20 && isset($result->response->docs) && is_array($result->response->docs)) {
            foreach ($result->response->docs as $doc) {
                if (isset($doc->title_full) && count($doc->title_full) > 0) {
                    $record = array();
                    if (isset($doc->id)) {
                        $record['identifier'] = $doc->id;
                    }
                    $record['title'] = $doc->title_full[0];

                    if (isset($doc->series) && count($doc->series) > 0) {
                        $record['prefix'] = $doc->series[0];
                    } else {
                        $record['prefix'] = '';
                    }

                    if (isset($doc->date) && count($doc->date) > 0) {
                        if ($record['prefix'] !== '') {
                            $record['prefix'] .= ', ';
                        }
                        $record['prefix'] .= $doc->date[0];
                        $record['prefix'] .= '.';
                    }
                    $records[] = $record;
                }
            }
        }

        return [[ "count" => $numFound, "records" => $records ], "200"];
    }
}
