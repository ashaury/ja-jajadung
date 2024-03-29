<?php
/**
 * @version   $Id: ContentBuilder.php 54644 2012-08-02 19:56:08Z steph $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

class RokSprocket_Provider_ContentBuilder extends RokSprocket_Provider_AbstarctJoomlaBasedProvider
{
    /**
     * @static
     * @return bool
     */
    public static function isAvailable()
    {
        if (!class_exists('JFactory')) {
            return false;
        }
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.extension_id');
        $query->from('#__extensions AS a');
        $query->where('a.type = "component"');
        $query->where('a.element = "com_contentbuilder"');
        $query->where('a.enabled = 1');

        $db->setQuery($query);

        if ($db->loadResult()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param array $filters
     * @param array $sort_filters
     */
    public function __construct($filters = array(), $sort_filters = array())
    {
        parent::__construct('contentbuilder');
        $this->setFilterChoices($filters, $sort_filters);
    }

    /**
     * @param     $raw_item
     * @param int $dborder
     *
     * @return \RokSprocket_Item
     */
    protected function convertRawToItem($raw_item, $dborder = 0)
    {
        require_once (JPath::clean(JPATH_SITE . '/components/com_content/helpers/route.php'));
        require_once (JPath::clean(JPATH_SITE . '/libraries/joomla/html/html/content.php'));

        //$textfield = $this->params->get('contentbuilder_articletext_field', '');

        $item = new RokSprocket_Item();

        $item->setProvider($this->provider_name);
        $item->setId($raw_item->id);
        $item->setAlias($raw_item->alias);
        $item->setAuthor(($raw_item->created_by_alias) ? $raw_item->created_by_alias : $raw_item->author_name);
        $item->setTitle($raw_item->title);
        $item->setDate($raw_item->created);
        $item->setPublished(($raw_item->state == 1) ? true : false);
        $item->setCategory($raw_item->category_title);
        $item->setHits($raw_item->hits);
        $item->setRating($raw_item->rating);
        $item->setMetaKey($raw_item->metakey);
        $item->setMetaDesc($raw_item->metadesc);
        $item->setMetaData($raw_item->metadata);
        $item->setPublishUp($raw_item->publish_up);
        $item->setPublishDown($raw_item->publish_down);

        $texts = array();
        $text_fields = self::getFieldTypes(array("textarea", "text"));

        if (count($text_fields)) {
            foreach ($text_fields as $field) {
                $text = $this->getFieldValue($raw_item->id, $field->id);
                $texts['text_' . $field->id] = $text;
            }
        }

        $texts['text_introtext'] = $raw_item->introtext;
        $texts['text_fulltext'] = $raw_item->fulltext;
        $texts['text_metadesc'] = $raw_item->metadesc;

        $item->setTextFields($texts);
        $item->setText($texts['text_introtext']);



        $images = array();
        if (isset($raw_item->images) && !empty($raw_item->images)) {
            try {
                $raw_images = RokCommon_JSON::decode($raw_item->images);
                if (isset($raw_images->image_intro) && !empty($raw_images->image_intro)) {
                    $image_intro = new RokSprocket_Item_Image();
                    $image_intro->setSource($raw_images->image_intro);
                    $image_intro->setIdentifier('image_intro');
                    $image_intro->setCaption($raw_images->image_intro_caption);
                    $image_intro->setAlttext($raw_images->image_intro_alt);
                    $images[$image_intro->getIdentifier()] = $image_intro;
                }
                if (isset($raw_images->image_fulltext) && !empty($raw_images->image_fulltext)) {
                    $image_fulltext = new RokSprocket_Item_Image();
                    $image_fulltext->setSource($raw_images->image_fulltext);
                    $image_fulltext->setIdentifier('image_fulltext');
                    $image_fulltext->setCaption($raw_images->image_fulltext_caption);
                    $image_fulltext->setAlttext($raw_images->image_fulltext_alt);
                    $images[$image_fulltext->getIdentifier()] = $image_fulltext;
                    $item->setPrimaryImage($image_fulltext);
                }
            } catch (RokCommon_JSON_Exception $jse) {
                $this->container->roksprocket_logger->warning('Unable to decode image JSON for article ' . $item->getArticleId());
            }
        }
        $image_fields = self::getFieldTypes("upload");
        if (count($image_fields)) {
            foreach ($image_fields as $field) {
                $image_uri = $this->getFieldValue($raw_item->record_id, $field->storage_field, $field->storage_table);
                if (JFile::exists(JPath::clean(JPATH_SITE . '/' . $image_uri)) && is_file(JPath::clean(JPATH_SITE . '/' . $image_uri))) {
                    $image_field = new RokSprocket_Item_Image();
                    $image_field->setSource(JPath::clean(JURI::root(true) . '/' . $image_uri));
                    $image_field->setIdentifier('image_' . $field->id);
                    $image_field->setCaption('');
                    $image_field->setAlttext('');
                    $images['image_' . $field->id] = $image_field;
                }
            }
        }
        $item->setImages($images);

        $primary_link = new RokSprocket_Item_Link();
        $primary_link->setUrl(JRoute::_('index.php?option=com_contentbuilder&title=list-view-easyblog&controller=details&id='.$raw_item->form_id.'&record_id='.$raw_item->record_id));
        $primary_link->getIdentifier('article_link');

        $item->setPrimaryLink($primary_link);

        $links = array();
        if (isset($raw_item->urls) && !empty($raw_item->urls)) {
            try {
                $raw_links = RokCommon_JSON::decode($raw_item->urls);
                if (isset($raw_links->urla)) {
                    $linka = new RokSprocket_Item_Link();
                    $linka->setUrl($raw_links->urla);
                    $linka->setText($raw_links->urlatext);
                    $linka->setIdentifier('urla');
                    $links[$linka->getIdentifier()] = $linka;

                }
                if (isset($raw_links->urlb)) {
                    $linkb = new RokSprocket_Item_Link();
                    $linkb->setUrl($raw_links->urlb);
                    $linkb->setText($raw_links->urlbtext);
                    $linkb->setIdentifier('urlb');
                    $links[$linkb->getIdentifier()] = $linkb;
                }
                if (isset($raw_links->urlc)) {
                    $linkc = new RokSprocket_Item_Link();
                    $linkc->setUrl($raw_links->urlc);
                    $linkc->setText($raw_links->urlctext);
                    $linkc->setIdentifier('urlc');
                    $links[$linkc->getIdentifier()] = $linkc;
                }

            } catch (RokCommon_JSON_Exception $jse) {
                $this->container->roksprocket_logger->warning('Unable to decode link JSON for article ' . $item->getArticleId());
            }
        }
        $link_fields = self::getFieldTypes("text");
        if (count($link_fields)) {
            foreach ($link_fields as $field) {
                $raw_link = $this->getFieldValue($raw_item->record_id, $field->storage_field, $field->storage_table);
                if (filter_var($raw_link, FILTER_VALIDATE_URL)) {
                    $link_field = new RokSprocket_Item_Link();
                    $link_field->setUrl($raw_link);
                    $link_field->setText(null);
                    $link_field->setIdentifier('url_' . $field->id);
                    $links[$link_field->getIdentifier()] = $link_field;
                }
            }
        }

        $item->setLinks($links);

        $item->setDbOrder($dborder);

        // unknown joomla items
        $item->setCommentCount(0);
        $item->setTags(array());

        return $item;
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function getArticleEditUrl($id)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('form_id');
        $query->from('#__contentbuilder_articles');
        $query->where('record_id = ' . $id);

        $db->setQuery($query);

        $form_id = $db->loadResult();
        return JURI::root(true) . '/administrator/index.php?option=com_contentbuilder&controller=details&id=' . $form_id . '&record_id=' . $id;
    }

    public static function getImageTypes()
    {
        $fields = self::getFieldTypes("upload");

        $list = array();
        foreach ($fields as $field) {
            $list['image_' . $field->value] = array();
            $list['image_' . $field->value]['group'] = $field->id;
            $list['image_' . $field->value]['display'] = $field->title;
        }
        $static = array(
            'image_intro' => array(
                'group' => null,
                'display' => 'Article Intro Image'
            ),
            'image_fulltext' => array(
                'group' => null,
                'display' => 'Article Full Image'
            )
        );
        $list = array_merge($static, $list);

        return $list;
    }

    /**
     * @return array the array of link types and label
     */
    public static function getLinkTypes()
    {
        $fields = self::getFieldTypes("text");

        $list = array();
        foreach ($fields as $field) {
            $list['url_' . $field->value] = array();
            $list['url_' . $field->value]['group'] = $field->id;
            $list['url_' . $field->value]['display'] = $field->title;
        }

        $static = array(
            'urla' => array(
                'group' => null,
                'display' => 'Link A'
            ),
            'urlb' => array(
                'group' => null,
                'display' => 'Link B'
            ),
            'urlc' => array(
                'group' => null,
                'display' => 'Link C'
            )
        );

        $list = array_merge($static, $list);

        return $list;
    }

    /**
     * @return array the array of link types and label
     */
    public static function getTextTypes()
    {
        $fields = self::getFieldTypes(array("textarea", "text"));

        $list = array();
        foreach ($fields as $field) {
            $list['text_' . $field->value] = array();
            $list['text_' . $field->value]['group'] = $field->id;
            $list['text_' . $field->value]['display'] = $field->title;
        }
        $static = array(
            'text_fulltext' => array('group' => null, 'display' => 'Full Text'),
            'text_introtext' => array('group' => null, 'display' => 'Intro Text'),
            'text_metadesc' => array('group' => null, 'display' => 'Meta Description'),
        );
        $list = array_merge($static, $list);
        return $list;
    }

    private static function getFieldTypes($field = false)
    {

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('sf.id, sf.id AS value, CONCAT(cf.name, "-", ce.label) AS title, sf.name AS storage_field, s.name AS storage_table');
        $query->from('#__contentbuilder_storage_fields AS sf');
        $query->join('LEFT', '#__contentbuilder_storages AS s ON s.id = sf.storage_id');
        $query->join('LEFT', '#__contentbuilder_elements AS ce ON ce.reference_id = sf.id');
        $query->join('LEFT', '#__contentbuilder_forms AS cf ON cf.id = ce.form_id');


        if ($field && is_array($field)) {
            $wheres = array();
            foreach ($field as $match) {
                $wheres[] = ('ce.type = "' . $match . '"');
            }
            $query->where('(' . implode(' OR ', $wheres) . ')');
        } else if ($field && is_string($field)) {
            $query->where('ce.type = "' . $field . '"');
        }

        $query->group('sf.id');
        $query->order('sf.title ASC');

        $db->setQuery($query);
        return  $db->loadObjectList();
    }

    private static function getFieldValue($id = false, $field = false, $table = false)
    {
        if (!$field || !$table || !$id) return '';

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($field);
        $query->from("#__" . $table);
        $query->where('id = ' . $id);

        $db->setQuery($query);
        return $db->loadResult();
    }
}

