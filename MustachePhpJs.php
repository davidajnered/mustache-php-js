<?php
/**
 * MustachePhpJs - for easy sharing of mustache templates between php and js for logic-less templates.
 * Written by David Ajnered
 */
namespace MustachePhpJs;

class MustachePhpJs
{
    /**
     * @var Mustache_Engine
     */
    private $engine;

    /**
     * @var bool
     */
    private $started;

    /**
     * @var string
     */
    private $templateName;

    /**
     * @var string
     */
    private $template;

    /**
     * Singleton.
     *
     * @return PerfectExcerpt
     */
    public static function getObject()
    {
        static $instance;

        if (!$instance) {
            $instance = new MustachePhpJs();
        }

        return $instance;
    }

    /**
     * Constructor.
     */
    private function __construct()
    {
        $this->loadPhpEngine();
        $this->loadJsEngine();
    }

    /**
     * Load mustache PHP engine.
     */
    private function loadPhpEngine()
    {
        require_once(plugin_dir_path(__FILE__) . 'lib/mustache-php/Mustache/Autoloader.php');
        \Mustache_Autoloader::register();
        $this->engine = new \Mustache_Engine();
    }

    /**
     * Enqueue mustache.js.
     */
    private function loadJsEngine()
    {
        wp_enqueue_script('mustache-php-js', plugin_dir_url(__FILE__) . 'lib/mustache-js/mustache.js');
    }

    /**
     * Start buffering template.
     */
    public function capture()
    {
        if ($this->started) {
            trigger_error('mustache output buffer already initialized');
            return false;
        }

        $this->started = true;

        ob_start();
    }

    /**
     * Get template wrapped in script tags for mustache.js.
     */
    public function getScript()
    {
        echo '<script id="' . $this->templateName . '" type="x-tmpl-mustache">';
        echo $this->template;
        echo '</script>';
    }

    /**
     * Stop output buffer and render template.
     */
    public function render($templateName, $templateData)
    {
        $this->templateName = $templateName;

        // Get template and clean output buffer
        $this->template = ob_get_clean();

        // Disable lock and enable rendering of another template
        $this->started = false;

        echo $this->engine->render($this->template, $templateData);
    }
}
