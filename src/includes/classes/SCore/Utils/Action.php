<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\Core\Classes\SCore\Utils;

use WebSharks\WpSharks\Core\Classes;
use WebSharks\WpSharks\Core\Interfaces;
use WebSharks\WpSharks\Core\Traits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Action utils.
 *
 * @since 160531 Action utils.
 */
class Action extends Classes\SCore\Base\Core
{
    /**
     * Action var.
     *
     * @since 160531 Action utils.
     *
     * @type string Action var.
     */
    protected $var;

    /**
     * Data var.
     *
     * @since 160531 Action utils.
     *
     * @type string Data var.
     */
    protected $data_var;

    /**
     * Data slug.
     *
     * @since 160531 Action utils.
     *
     * @type string Data slug.
     */
    protected $data_slug;

    /**
     * Current action.
     *
     * @since 160531 Action utils.
     *
     * @type string Current action.
     */
    protected $action;

    /**
     * Class constructor.
     *
     * @since 160531 Action utils.
     *
     * @param Classes\App $App Instance.
     */
    public function __construct(Classes\App $App)
    {
        parent::__construct($App);

        $this->var       = $this->App->Config->©brand['©var'].'_action';
        $this->data_var  = $this->App->Config->©brand['©var'].'_action_data';
        $this->data_slug = $this->App->Config->©brand['©slug'].'-action-data';
        $this->action    = $_REQUEST[$this->var] ?? '';
    }

    /**
     * Handle actions.
     *
     * @since 160531 Action utils.
     */
    public function onWpLoaded()
    {
        if (!$this->action) {
            return; // Nothing to do.
        }
        if (mb_strpos($this->action, '_via_ajax') !== false) {
            header('content-type: application/json; charset=utf-8');
            $this->c::isAjax(true); // AJAX flag.
        }
        $this->c::noCacheHeaders();
        $this->s::requireValidNonce($this->action);
        $this->c::doingAction($this->action);
        $this->handle(); // See handler below.
    }

    /**
     * Handle actions.
     *
     * @since 160531 Action utils.
     *
     * @note Only runs when appropriate.
     */
    protected function handle() // For extenders.
    {
        switch ($this->action) {
            case '§save-options':
                $this->App->Utils->§Options->onActionSaveOptions();
                break; // Stop here.

            case '§save-options-via-ajax':
                $this->App->Utils->§Options->onActionSaveOptionsViaAjax();
                break; // Stop here.

            case '§restore-default-options':
                $this->App->Utils->§Options->onActionRestoreDefaultOptions();
                break; // Stop here.

            case '§dismiss-notice':
                $this->App->Utils->§Notices->onActionDismissNotice();
                break; // Stop here.

            default: // Default case handler.
                if (mb_strpos($this->action, '§') === 0) {
                    $this->s::dieForbidden();
                }
        } // Only fail if it's a core action. Otherwise let extenders handle.
    }

    /**
     * Action data.
     *
     * @since 160531 Action utils.
     *
     * @return mixed Action data.
     */
    public function data()
    {
        if (!$this->action) {
            return null; // Not applicable.
        } elseif (!$this->c::doingAction()) {
            return null; // Not appliable.
        }
        if (($data = $_REQUEST[$this->data_var] ?? null)) {
            $data = $this->c::mbTrim($this->c::unslash($data));
        }
        return $data; // Trimmed and stripped data (possible `null` value).
    }

    /**
     * Add action to a URL.
     *
     * @since 160531 Action utils.
     *
     * @param string     $url    Input URL.
     * @param string     $action Action identifier.
     * @param mixed|null $data   Action data (optional).
     *
     * @return string URL w/ an action.
     */
    public function urlAdd(string $url, string $action, $data = null): string
    {
        $url        = $this->c::addUrlQueryArgs([$this->var => $action], $url);
        $url        = isset($data) ? $this->c::addUrlQueryArgs([$this->data_var => $data], $url) : $url;
        return $url = $this->s::addUrlNonce($url, $action);
    }

    /**
     * Remove an nonce from a URL.
     *
     * @since 160524 First documented version.
     *
     * @param string $url Input URL.
     *
     * @return string URL w/o an nonce.
     */
    public function urlRemove(string $url): string
    {
        $url        = $this->c::removeUrlQueryArgs([$this->var, $this->data_var], $url);
        return $url = $this->s::removeUrlNonce($url);
    }

    /**
     * Data form element ID.
     *
     * @since 160531 Action utils.
     *
     * @param string $action Action slug.
     * @param string $var    Data var (array key).
     *
     * @return string Data form element ID.
     */
    public function formElementId(string $action, string $var): string
    {
        return $this->data_slug.'-'.$action.'-'.$this->c::varToSlug($var);
    }

    /**
     * Data form element class.
     *
     * @since 160531 Action utils.
     *
     * @param string $var Data var (array key).
     *
     * @return string Data form element class.
     */
    public function formElementClass(string $var): string
    {
        return $this->data_slug.'-'.$this->c::varToSlug($var);
    }

    /**
     * Data form element name.
     *
     * @since 160531 Action utils.
     *
     * @param string $var Data var (array key).
     *
     * @return string Data form element name.
     */
    public function formElementName(string $var): string
    {
        return $this->data_var.'['.$var.']';
    }
}
