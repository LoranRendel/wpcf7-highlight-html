<?php
/*
 * Plugin Name: Contact Form 7 Highlight HTML
 * Description: Highlight HTML in Contact Form 7 edit page
 * Author: Loran A. Rendel
 * Author URI: https://xpor.org/
 * License: GPL v3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Version: 1.0.0
 * Requires at least: 6.2
 * Requires PHP: 7.3
 */

add_action(
    'admin_enqueue_scripts',
    function (string $hook): void {
        if (!apply_filters('wpcf7_highlight_html', true)) {
            return;
        }
        if ($hook === 'toplevel_page_wpcf7' && isset($_GET['post'])) {
            $settings = wp_enqueue_code_editor(
                [
                    'type' => 'text/html',
                    'htmlhint' => ['space-tab-mixed-disabled' => 'space'],
                    'codemirror' => ['indentWithTabs' => false]
                ]
            );
            if ($settings !== false) {
                wp_add_inline_script(
                    'code-editor',
                    sprintf(
                        <<<'EOF'
                            jQuery(function () {
                              const codemirror = wp.codeEditor.initialize('wpcf7-form', %s).codemirror
                              wpcf7.taggen.insert = content => {
                                let cursor = codemirror.getCursor()
                                codemirror.replaceRange(content, cursor)
                                cursor = codemirror.getCursor()
                                codemirror.focus()
                                codemirror.setCursor(cursor)
                              }
                            })
                            EOF,
                        wp_json_encode($settings)
                    )
                );
            }
        }
    }
);