<?php

class MENUINPOST_Help_Tabs
{

    private $screen;
    
    public function __construct(WP_Screen $screen)
    {
        $this->screen = $screen;
    }

    public function mipSetHelpTabs($type)
    {
        switch ($type) {
        case 'options':
            $this->screen->add_help_tab(
                array(
                'id'=>'mip_options', 
                'title'=>__('Menu In Post Options', 'menu-in-post'), 
                'content'=>$this->_content('options'))
            );
            break;
        case 'tools':
            $this->screen->add_help_tab(
                array(
                'id'=>'tools_shortcode_builder', 
                'title'=>__('Shortcode Builder', 'menu-in-post'), 
                'content'=>$this->_content('shortcode_builder'))
            );
            return;
            break;
        }
    }

    private function _content($name)
    {
        $content = array();
        
        switch($name) {
        case 'shortcode_builder':
            $content['shortcode_builder'] = '<h2>' . 
                __(
                    'Shortcode Builder Options', 
                    'menu-in-post'
                ) . '</h2>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'Use the Shortcode Builder to create a menu shortcode. ' . 
                    'Copy-and-paste the shortcode into a Shortcode Block in a ' . 
                    'WordPress post or page. To add a Shortcode Block to post ' . 
                    'or page, in the WordPress Editor, go Add Block -> Widgets ' . 
                    '-> Shortcode.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Select Menu:</strong> Select the menu you want to ' . 
                    'use from the drop-down menu.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Include Container:</strong> By default, the menus ' . 
                    'created by Menu In Post are contained in div elements. ' . 
                    'Select &#34;no&#34; if you do not want a container.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Container ID:</strong> The ID that you would like ' . 
                    'the container div to be given. The ID must be unique within ' . 
                    'the HTML document, must contain at least one character, ' . 
                    'and must not contain any spaces.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Container Class(es):</strong> The class ' . 
                    'attribute(s) you want the container div to be given. ' . 
                    'Separate multiple classes with a space. Classes must ' . 
                    'begin with a letter A-Z or a-z, and can be followed by ' . 
                    'letters (A-Za-z), digits (0-9), hyphens (&#34;-&#34;), ' . 
                    'and underscores (&#34;_&#34;).', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Menu ID:</strong> The ID that you want assigned to ' . 
                    'the ul element of the menu list. The ID must be unique ' . 
                    'within the HTML document, must contain at least one ' . 
                    'character, and must not contain any spaces.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Menu Class(es):</strong> The class attribute(s) you ' . 
                    'want assigned to the ul element of the menu list. Separate ' . 
                    'multiple classes with a space. Classes must begin with a ' . 
                    'letter A-Z or a-z, and can be followed by letters (A-Za-z),' . 
                    ' digits (0-9), hyphens (&#34;-&#34;), and underscores ' . 
                    '(&#34;_&#34;). Not used for dropdown style menus.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Depth:</strong> Set the number of menu levels you ' . 
                    'would like to display. The default is &#34;all,&#34; ' . 
                    'which will display all top-level menus and submenus.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Style:</strong> Choose the type of menu, either an ' . 
                    'unordered list of links, or a dropdown box.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Placeholder Text:</strong> Optional for dropdown. ' . 
                    'Sets the text used for the first, placeholder option in ' . 
                    'a dropdown menu. Leave blank for the default, &#39;Select' . 
                    '...&#39;.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    '<strong>Append to URL:</strong> Enter a text string to ' . 
                    'append to every URL in the menu. For example, to add the ' . 
                    'anchor &quot;myanchor&quot; you would enter &quot;#myanchor' . 
                    '&quot;. You could add a variable using &quot;?myvariable=' . 
                    'myvalue&quot;. The <em>same</em> text string will be ' . 
                    'appended to every menu item. To set different anchors or ' . 
                    'variables to a menu item, you should use the Custom Link ' . 
                    'option for the menu item.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<h2>' . 
                __(
                    'Open Links In a New Tab or Window', 
                    'menu-in-post'
                ) . '</h2>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'Menu In Post honors WordPress menu item settings, ' . 
                    'so you can set the desired target of each menu item ' . 
                    'in both list- and dropdown-style menus.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'In order to force Menu in Post to open links in both ' . 
                    'list- and dropdown-style menus in a new tab or window, ' . 
                    'open the menu in Admin > Appearance > Menus.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'Note: Some block-enabled themes disable the Menus entry in ' . 
                    'the Appearance menu, so if you do not see it, try ' . 
                    'going to Settings > Menu In Post in WordPress Admin, ' . 
                    'and setting &quot;Show Appearance > Menus&quot; to ' . 
                    '&quot;yes&quot;.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'On the Appearance > Menus page with your menu selected, ' . 
                    'enable the the Advanced Menu Properties option ' . 
                    '&quot;Link Target&quot; if it is not already enabled ' . 
                    'by clicking the Screen Options tab and selecting it.', 
                    'menu-in-post'
                ) . '</p>';
            
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'Check the option &quot;Open link in a new tab&quot; for ' . 
                    'each menu item you want to open in a new tab or window.', 
                    'menu-in-post'
                ) . '</p>';
            $content['shortcode_builder'] .= '<p>' . 
                __(
                    'The settings in the user&#39;s browser will determine ' . 
                    'whether the link opens in a new tab or window.', 
                    'menu-in-post'
                ) . '</p>';
            break;
        case 'options':
            $content['options'] = '<p>' . 
                __(
                    'Menu In Post will work just as it always has without any ' . 
                    'changes to these options.', 
                    'menu-in-post'
                ) . '</p>';
            $content['options'] .= '<h3>' . 
                __(
                    'Load JavaScript', 
                    'menu-in-post'
                ) . '</h3>';
            $content['options'] .= '<ul><li>' . 
                __(
                    'JavaScript is not required if all your Menu In Post ' . 
                    'menus are &quot;List of Links&quot; style. If you are only ' . 
                    'using &quot;List of Links&quot;-style menus, you can ' . 
                    'select &quot;never&quot; to prevent Menu In Post&#39;s ' . 
                    'JavaScript from loading on any page to maximize performance.', 
                    'menu-in-post'
                ) . '</li>';
            $content['options'] .= '<li>' . 
                __(
                    'If you intend to use any &quot;Dropdown&quot;-style menus, ' . 
                    'you must include JavaScript using either the ' . 
                    '&quot;always&quot; or &quot;only on posts/pages&quot; ' . 
                    'Load JavaScript options.', 
                    'menu-in-post'
                ) . '</li>';
            $content['options'] .= '<li>' . 
                __(
                    'If you use the &quot;' . 
                    'only on posts/pages&quot; Load JavaScript option, you ' . 
                    'must specify the ID(s) of the posts or pages where you ' . 
                    'add the Menu In Post shortcode(s) in order for the ' . 
                    'dropdown-style menus to work.', 
                    'menu-in-post'
                ) . '</li></ul>';
            $content['options'] .= '<h3>' . 
                __(
                    'Minimize JavaScript', 
                    'menu-in-post'
                ) . '</h3>';
            $content['options'] .= '<p>' . 
                __(
                    'Select &quot;yes&quot; to load a minimized version of ' . 
                    'Menu In Post&#39;s JavaScript file. &quot;No&quot; will ' . 
                    'load an un-minified version of the file.', 
                    'menu-in-post'
                ) . '</p>';
            $content['options'] .= '<p>' . 
                __(
                    'The Minimize JavaScript setting will also determine ' . 
                    'whether or not minimized versions of JavaScript and CSS ' . 
                    'are used on Menu In Post&#39;s Admin pages.', 
                    'menu-in-post'
                ) . '</p>';
            $content['options'] .= '<h3>' . 
                __(
                    'Show Appearance > Menus', 
                    'menu-in-post'
                ) . '</h3>';
            $content['options'] .= '<p>' . 
                __(
                    'Since WordPress 5.9, the Appearance > Menus menu ' . 
                    'link for the classic menu editor, required to add/edit ' . 
                    'menus for Menu In Post, has been hidden for block-enabled' . 
                    ' themes. Set the &quot;Show Appearance > Menus&quot; ' . 
                    'option to &quot;yes&quot; to display the Menus link ' . 
                    'in the Appearance menu in Admin to add/edit menus for ' . 
                    'Menu In Post. If you are not using a block-enabled theme, ' . 
                    'the &quot;Show Appearance > Menus&quot; option will not ' . 
                    'be displayed.'
                ) . '</p>';
            break;
        }
        
        if (!empty($content[$name])) {
            return $content[$name];
        }
    }
}