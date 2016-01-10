<?php

/*
 * Kunena Spotify Plugin
 * Author  : Giulio 'juliux' Erler
 * Contact : juliux {DOT} pigface {AT} gmail {DOT} com
 * Web : http://pigjuliux.wordpress.com
 * License of this plugin : GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgKunenaKunenaSpotify extends JPlugin
{

    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
    }

    public function onKunenaBbcodeEditorInit($editor)
    {
        $this->loadLanguage();
        $btn = new KunenaBbCodeEditorButton('spotify', 'spotify', 'spotify', 'PLG_KUNENASPOTIFY_BTN_TITLE', 'PLG_KUNENASPOTIFY_BTN_ALT');
        $btn->addWrapSelectionAction();
        $editor->insertElement($btn, 'after', 'code');

        $url = $this->params->get('mathjax', 'https://c328740.ssl.cf1.rackcdn.com/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
        $document = JFactory::getDocument();
        $document->addScript($url);

        $document->addStyleDeclaration("#Kunena #kbbcode-toolbar #spotify {
            background-image: url(\"" . JURI::base(true) . "/plugins/kunena/kunenaspotify/images/spotify.png\");
        }");

        //Add JS Code for preview
        $document->addScriptDeclaration("window.addEvent('domready', function() {
            preview = document.id('kbbcode-preview');
            preview.addEvent('updated', function(event){
                MathJax.Hub.Queue(['Typeset',MathJax.Hub,'kbbcode-preview']);
                document.getElements('.latex').each(function(item, index) {
                    item.setStyle('display', '');
                });
            });
        });");

    }

    public function onKunenaBbcodeConstruct($bbcode)
    {
        $bbcode->AddRule('spotify', array(
                'mode' => BBCODE_MODE_CALLBACK,
                'method' => 'plgKunenaKunenaSpotify::onSpotify',
                'allow' => array('type' => '/^[\w]*$/',),
                'allow_in' => array('listitem', 'block', 'columns'),
                'content' => BBCODE_VERBATIM,
                'before_tag' => "sns",
                'after_tag' => "sn",
                'before_endtag' => "sn",
                'after_endtag' => "sns",
                'plain_start' => "\n",
                'plain_end' => "\n")
        );

        $url = $this->params->get('mathjax', 'https://c328740.ssl.cf1.rackcdn.com/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');

        $document = JFactory::getDocument();
        $document->addScript($url);
        $document->addScriptDeclaration("window.addEvent('domready', function() {
            document.getElements('.latex').each(function(item, index) {
                item.setStyle('display', '');
            });
        });");

        return true;
    }

    static public function onSpotify($bbcode, $action, $name, $default, $params, $content)
    {

        if ($action == BBCODE_CHECK) {
            $bbcode->autolink_disable = 1;
            return true;
        }

        $bbcode->autolink_disable = 0;
        $config = JPluginHelper::getPlugin('kunena', 'kunenaspotify');
        $config = json_decode($config->params);
        $html = '<iframe src="https://embed.spotify.com/?uri=spotify:track:'.$content.'" width="300" height="380" frameborder="0" allowtransparency="true"></iframe>';
        
        return $html;
    }
}

