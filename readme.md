WP Design Awards (free version)
================================

##Description:

__This is the the free version of wordpress plugin, WP Design Awards. Offers the functionality of a web design contest.__

###Adds two shortcodes:

The css_gallery shortcode for displaying the sites gallery!
_[css_gallery category_slug=<category_slug> sites_per_page=9 no_pagination=false]_

And the nominee_form for the users to submit their own creations.
_[nominee_form]_


###Features:

* Adds a sites gallery shortcode with the capability to rate the displayed sites!
* Adds a submission form for visitors and users to submit their own creations
* Adds a custom post type for the sites
* The custom post type has its own template, in which users can rate also!
* Adds a custom taxonomy
* Generates an easy to understand settings page.
* Provides control of the dimensions of accepted featured image
* Provides control of the type of accepted featured image
* Calculates the thumbnail based on a percentage of featured image provided by the admin!
* Provides the choice of default category, if there is one!
* Provides an extra category tag for the users to choose and display to the post page!



##Installation


1. Download, install and Activate the plugin
2. Create Categories

 _You must create some categories for the wp design awards, custom post type!The most often is: gallery (for general category), nominees and winners for subcategories in gallery! But after all is your choice!
Just go to Design awards, click to the Participants taxonomy, and create few categories!_

 - Gallery
	- Nominees
	- Winners

 __You can choose the default categoy in design awards settings page__

3. Go to the settings page and make your own adjustments and save them.
Before exiting the settings page, for safety, check if your adjustments saved well, if  no, correct them and click again save! Is very important to have correct settings!

4. Setting up pages:
 Of course you can use the shortcodes in posts, your convenience is the very reason for the creation of shortcodes. However is much more convenient to use these shortcodes in pages for easy access!

 ####CSS GALLERY SHORTCODE
For displaying the gallery of a category, you must add a new page/post with the proper shorcode. Add a new page or post and type of copy/page (personally, i have saved the shortcodes as snippets. You don’t need to remember them!):

 [css_gallery category_slug=your_category_of_choice_slug]

 For example:

 [css_gallery category_slug=winners]

 This is the simple version of this shortcode, there are some additional attributes:

 sites_per_page (default 9 post per page)

 no_pagination, accepts values true or false and if true it disables the pagination! The default is false.

 ####NOMINEE FORM SHORTCODE
This is a simple one, you just add a page or post, use the shortcode [nominee_form] and save! So simple!


###WARNING:

__THE PLUGINS FOLDER MUST BE NAMED "design_awards"__

_Also, there is a video! It explains the installation. However is for the premium version! Some things will be a little different!_
http://www.e-xtnd.it/wp-design-awards/