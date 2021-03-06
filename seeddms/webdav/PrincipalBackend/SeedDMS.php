<?php

namespace SeedDMS\PrincipalBackend;

use Sabre\DAV;
use Sabre\DAV\MkCol;
use Sabre\HTTP\URLUtil;

/**
 * PDO principal backend
 *
 *
 * This backend assumes all principals are in a single collection. The default collection
 * is 'principals/', but this can be overridden.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class SeedDMS extends \Sabre\DAVACL\PrincipalBackend\AbstractBackend implements \Sabre\DAVACL\PrincipalBackend\CreatePrincipalSupport {

    /**
     * dms
     *
     * @var SeedDMЅ_Core_DMS
     */
    protected $dms;

    /**
     * A list of additional fields to support
     *
     * @var array
     */
    protected $fieldMap = [

        /**
         * This property can be used to display the users' real name.
         */
        '{DAV:}displayname' => [
            'dbField' => 'displayname',
        ],

        /**
         * This is the users' primary email-address.
         */
        '{http://sabredav.org/ns}email-address' => [
            'dbField' => 'email',
        ],
    ];

    /**
     * Sets up the backend.
     *
     * @param SeedDMS_Core_DMS $dms
     */
    function __construct(\SeedDMS_Core_DMS $dms) {

        $this->dms = $dms;

    }

    /**
     * Returns a list of principals based on a prefix.
     *
     * This prefix will often contain something like 'principals'. You are only
     * expected to return principals that are in this base path.
     *
     * You are expected to return at least a 'uri' for every user, you can
     * return any additional properties if you wish so. Common properties are:
     *   {DAV:}displayname
     *   {http://sabredav.org/ns}email-address - This is a custom SabreDAV
     *     field that's actualy injected in a number of other properties. If
     *     you have an email address, use this property.
     *
     * @param string $prefixPath
     * @return array
     */
    function getPrincipalsByPrefix($prefixPath) {

        $users = $this->dms->getAllUsers();
        $principals = array();
        foreach($users as $u) {
            if(!$u->isDisabled()) {
                $principal = array('id'=>$u->getId(), 'uri'=>'principals/'.$u->getLogin(), '{DAV:}displayname'=>$u->getFullname(), '{http://sabredav.org/ns}email-address'=>$u->getEmail());
                $principals[] = $principal;
            }
        }
        return $principals;
    }

    /**
     * Returns a specific principal, specified by it's path.
     * The returned structure should be the exact same as from
     * getPrincipalsByPrefix.
     *
     * @param string $path
     * @return array
     */
    function getPrincipalByPath($path) {

        list($prefix, $login) = URLUtil::splitPath($path);
        if(!($u = $this->dms->getUserByLogin($login)))
            return;

        $principal = [
            'id'  => $u->getID(),
            'uri' => 'principals/'.$u->getLogin(),
            '{DAV:}displayname' => $u->getFullname(),
            '{http://sabredav.org/ns}email-address' => $u->getEmail(),
        ];
        return $principal;
    }

    /**
     * Updates one ore more webdav properties on a principal.
     *
     * The list of mutations is stored in a Sabre\DAV\PropPatch object.
     * To do the actual updates, you must tell this object which properties
     * you're going to process with the handle() method.
     *
     * Calling the handle method is like telling the PropPatch object "I
     * promise I can handle updating this property".
     *
     * Read the PropPatch documentation for more info and examples.
     *
     * @param string $path
     * @param DAV\PropPatch $propPatch
     */
    function updatePrincipal($path, DAV\PropPatch $propPatch) {

        $propPatch->handle(array_keys($this->fieldMap), function($properties) use ($path) {
            return true;
        });

    }

    /**
     * This method is used to search for principals matching a set of
     * properties.
     *
     * This search is specifically used by RFC3744's principal-property-search
     * REPORT.
     *
     * The actual search should be a unicode-non-case-sensitive search. The
     * keys in searchProperties are the WebDAV property names, while the values
     * are the property values to search on.
     *
     * By default, if multiple properties are submitted to this method, the
     * various properties should be combined with 'AND'. If $test is set to
     * 'anyof', it should be combined using 'OR'.
     *
     * This method should simply return an array with full principal uri's.
     *
     * If somebody attempted to search on a property the backend does not
     * support, you should simply return 0 results.
     *
     * You can also just return 0 results if you choose to not support
     * searching at all, but keep in mind that this may stop certain features
     * from working.
     *
     * @param string $prefixPath
     * @param array $searchProperties
     * @param string $test
     * @return array
     */
    function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof') {
        if (count($searchProperties) == 0) return [];    //No criteria

        return [];
    }

    /**
     * Finds a principal by its URI.
     *
     * This method may receive any type of uri, but mailto: addresses will be
     * the most common.
     *
     * Implementation of this API is optional. It is currently used by the
     * CalDAV system to find principals based on their email addresses. If this
     * API is not implemented, some features may not work correctly.
     *
     * This method must return a relative principal path, or null, if the
     * principal was not found or you refuse to find it.
     *
     * @param string $uri
     * @param string $principalPrefix
     * @return string
     */
    function findByUri($uri, $principalPrefix) {
        return 'principals/admin';
    }

    /**
     * Returns the list of members for a group-principal
     *
     * @param string $principal
     * @return array
     */
    function getGroupMemberSet($principal) {
        return [];
    }

    /**
     * Returns the list of groups a principal is a member of
     *
     * @param string $principal
     * @return array
     */
    function getGroupMembership($principal) {
        return [];
    }

    /**
     * Updates the list of group members for a group principal.
     *
     * The principals should be passed as a list of uri's.
     *
     * @param string $principal
     * @param array $members
     * @return void
     */
    function setGroupMemberSet($principal, array $members) {
        return;
    }

    /**
     * Creates a new principal.
     *
     * This method receives a full path for the new principal. The mkCol object
     * contains any additional webdav properties specified during the creation
     * of the principal.
     *
     * @param string $path
     * @param MkCol $mkCol
     * @return void
     */
    function createPrincipal($path, MkCol $mkCol) {
        return;
    }

}
