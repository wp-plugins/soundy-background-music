=== Soundy Background Music ===
Contributors: bducouedic
Tags: audio, sound, music, background, sound track, background sound, background audio, background music, posts, pages
Requires at least: 3.6
Tested up to: 3.8.1
Stable tag: 1.2
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://webartisan.ch/en/products/soundy#war_donate

Soundy allows a page or post to play a sound track while it is displayed.

== Description ==

The **Soundy** plugin allows to play a background music when a page or post is displayed.  
Each page or post can have its own sound track.   
The sound track is embedded in the page by means of the HTML 5 audio tag. This way, the plugin is compatible with all modern user devices (smartphones, tablets, laptops and desktops of all vendors).  
A background music can be associated with all pages and posts or with individual pages or posts.   
A Play/Pause button can also be displayed anywhere in the pages and posts.   
Defaults can be set in the settings page of the plugin.   
Specific post and page plugin settings can be configured in the Edit Page and Edit Post pages.   
In the plugin settings page, an audio track can be set per default.  
The audio track can be anywhere on the web as it is specified with its URL. It can also be uploaded in the media library of the WP site.  
A play and pause button image can be uploaded to replace the default one and can be positioned anywhere.    
This Play/Pause button can be positioned in a corner of the document or in a corner of the window. It can also be positioned with a template tag typically in the document header or with a shortcode in the content.  
For an example of a Soundy implementation, have a look to the swiss pianist, Magali Bourquin's website: [www.magalibourquin.com](http://magalibourquin.com/)  
A little Soundy example can also be seen in the Description tab of the [Soundy Plugin Home Page](http://webartisan.ch/en/products/soundy/).

= Docs & Support =

You can find [tutorial](http://webartisan.ch/en/products/soundy#war_tutorial), [FAQ](http://webartisan.ch/en/products/soundy#war_FAQ) and more detailed information about **Soundy** plugin on [WebArtisan.ch](http://webartisan.ch/en/products/soundy/). If you were unable to find the answer to your question on the FAQ or in any of the documentation, you should check the [support forum](http://wordpress.org/support/plugin/soundy-background-music) on WordPress.org. If you can't locate any topics that pertain to your particular issue, post a new topic for it.  
**Soundy** Plugin Home Page: [webartisan.ch/en/products/soundy](http://webartisan.ch/en/products/soundy/)

= Soundy Needs Your Support =

It is hard to continue development and support for this free plugin without contributions from users like you. If you enjoy using Soundy and find it useful, please consider [__making a donation__](http://webartisan.ch/en/products/soundy#war_donate). Your donation will help encourage and support the plugin's continued development and better user support.

== Installation ==
* In the Admin area (the back-end) of your WordPress Web Site, go to Plugins > Add New.
* Enter **Soundy** in the search field.
* **Soundy Background Music** appears.
* Click on "Install Now".
* Click on "Activate Plugin".
* To set up plugin defaults, go to Settings > **Soundy** in the admin area and fill out the input fields.
* For help, here is a [tutorial](http://webartisan.ch/en/products/soundy#war_tutorial).
* Happy Soundy Music !

= Updates =
* After an update of Soundy you must clear the cache of your browser for the Settings > Soundy page as well as the Edit Post and Edit Page pages. This is because cached Javascript and CSS files are modified at each Soundy update.

== Frequently Asked Questions ==

= Is it possible to have different sound tracks for different posts and pages. =

Yes, a soundtrack can be set up on a per post or per page basis.

= What kind of audio files can be used with Soundy Plugin ? =

The audio files must be in the format MP3, OGG or WAV. The file extension must be .mp3, .mpg, .mpeg, .ogg .wav or .wave.

= Is Soundy Plugin compatible with iphone, ipad and smartphones in general? =

Yes, it is as the plugin uses the HTML5 audio tag. There is just one issue with ipod, iphone and ipad (IOS Operating System): Autoplay option is deactivated by Apple on this operating system.

= Is it possible to position the Play/Pause button anywhere in the page or post header ? =

Yes, this can be done with the soundy_button() template tag.

= Is it possible to position the Play/Pause button anywhere in the content of a page or post ? =

Yes, this can be done with the [soundy button] shortcode.

= Is it possible to modify the Play/Pause button look & feel ? =

Yes, you can upload and set up your own button images.

= Can I really put my audio files anywhere on the web ? =

Yes, you can put your audio files anywhere on any cloud or website, except one place: you should not put your audio files in the soundy-background-music plugin directory (or below). If you do, you will loose your files the next time you will install a new version of Soundy. The update process deletes this folder and replace it with a new one. If you want to store the audio files on your WP site, the best place to do so is the media library (/wp-content/uploads/...).

== Screenshots ==

1. **Default Audio Track Settings**
1. **Play/Pause Button Settings**
1. **Play/Pause Button Corner Settings**
1. **Play/Pause Button Static Settings**
1. **Page or Post Soundy Metabox**

== Changelog ==

= 1.2 =
* Added Audio Volume Control for administrators in Settings > Soundy.
* Added Audio Volume Control for authors in Edit Page and Edit Post Soundy meta box.
* Made Admin User Interface more responsive and more user friendly.

= 1.1 =
* Corrected a few minor bugs
* Made Soundy forms of the back-end admin section more responsive
* Added possibility to disable Soundy for mobile devices. Set variable **$disable_soundy_for_mobile** to **true** in soundy.php file to disable Soundy for mobile devices.

= 1.0 =
* First Version

== Upgrade Notice ==

= 1.2 =
* After having upgraded Soundy to 1.2, please clear the cache of Soundy's admin pages in your browser. The reason is that a few CSS and Javascript files have been modified and these files are typically cached by browsers.

= 1.1 =
* After having upgraded Soundy to 1.1, please clear the cache of Soundy's admin pages in your browser. The reason is that a few CSS and Javascript files have been modified and these files are typically cached by browsers.