<?php
/**
 * Section close helper of the DokuTeaser Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Hamann <michael@content-space.de>
 */

class syntax_plugin_dokuteaser_closesection extends DokuWiki_Syntax_Plugin
{
    /** @inheritdoc  */
    function getType()
    {
        return 'substition';
    }

    /** @inheritdoc  */
    function getPType()
    {
        return 'block';
    }

    /** @inheritdoc  */
    function getSort()
    {
        return 195;
    }

    /**
     * Dummy handler, this syntax part has no syntax but is directly added to the instructions by the div syntax
     * @inheritdoc
     */
    function handle($match, $state, $pos, Doku_Handler $handler)
    {
    }

    /** @inheritdoc  */
    function render($mode, Doku_Renderer $renderer, $data)
    {
        if ($mode == 'xhtml') {
            /** @var Doku_Renderer_xhtml $renderer */
            $renderer->finishSectionEdit();
            return true;
        }
        return false;
    }

}

