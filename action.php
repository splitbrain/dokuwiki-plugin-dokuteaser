<?php
/**
 * Action part of the DokuTesaer plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author Michael Hamann <michael@content-space.de>
 */

/**
 * Action class of the DokuTeaser plugin, handles section edit buttons
 */
class action_plugin_dokuteaser extends DokuWiki_Action_Plugin
{
    /** @inheritdoc */
    function register(Doku_Event_Handler $controller)
    {
        $controller->register_hook('HTML_SECEDIT_BUTTON', 'BEFORE', $this, 'handle_secedit_button');
    }

    /**
     * Handle section edit buttons, prevents section buttons inside the DokuTeaser plugin from being rendered
     *
     * @param Doku_Event $event The event object
     * @param array $args Parameters for the event
     */
    public function handle_secedit_button(Doku_Event $event, $args)
    {
        // counter of the number of currently opened wraps
        static $wraps = 0;
        $data = $event->data;

        if ($data['target'] == 'plugin_dokuteaser_start') {
            ++$wraps;
        } elseif ($data['target'] == 'plugin_dokuteaser_end') {
            --$wraps;
        } elseif ($wraps > 0 && $data['target'] == 'section') {
            $event->preventDefault();
            $event->stopPropagation();
            $event->result = '';
        }
    }

}
