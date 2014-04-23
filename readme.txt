=== Soundy Background Music ===
Contributors: bducouedic
Tags: audio, sound, music, background, soundtrack, background sound, background audio, background music, posts, pages
Requires at least: 3.6
Tested up to: 3.9
Stable tag: 2.0
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Donate link: http://www.webartisan.ch/en/products/soundy-free/#wa_donate

Soundy allows a page or post to play a soundtrack while it is displayed.

== Description ==

The **Soundy** plugin allows a page or post to play a background music while it is displayed.  
Having a home page or any other page playing a background music greatly improves your website visitor's experience. By choosing a music in adequacy with your content, Soundy will help making your visitors immersed in your pages. Pages with a slideshow are excellent candidates for Soundy. But a lot of other page types too.  
A Play/Pause button can be displayed anywhere in the pages and posts.  
Do you need a Play/Pause button that perfectly fits your website design ? Try [Soundy PRO](http://www.webartisan.ch/en/products/soundy-pro/) for free.  
Each page or post can have its own soundtrack.   
The soundtrack is embedded in the page by means of the HTML 5 audio tag. This way, the plugin is compatible with all modern user devices (smartphones, tablets, laptops and desktops of all vendors).  
A background music can be associated with all pages and posts or with individual pages or posts.   
Defaults can be set by the administrator in the settings page of the plugin.   
Specific post and page plugin settings can be configured by the authors in the Edit Page and Edit Post pages.   
In the plugin settings page, an audio track can be set per default.  
The audio track can be anywhere on the web as it is specified with its URL. It can also be uploaded in the media library of the WP site.  
A play and pause button image can be uploaded by the administrator to replace the default one and can be positioned anywhere.  
Thanks to its Play/Pause Button Designer HTML5 application, with [Soundy PRO](http://www.webartisan.ch/en/products/soundy-pro/) you have full control over Soundy's Play/Pause button design, size, colors, rounding, transparency and positioning.  
This Play/Pause button can be positioned in a corner of the document or in a corner of the window. It can also be positioned with a template tag typically in the document header or with a shortcode in the content.  
Here are examples of Soundy implementations:

* Ivy & Mario's website: [www.hanstylewedding.com](http://www.hanstylewedding.com/)
* Swiss pianist, Magali Bourquin's website: [www.magalibourquin.com](http://www.magalibourquin.com/)

In Magali Bourquin's Soundy implementation, the background sound is enabled for all pages but only the homepage has the Autoplay option set. As for the Play/Pause button, custom images have been uploaded to replace the default ones and the button is statically positioned with a call to the soundy_button() function from the header.php file.

= Docs & Support =

You can find [Tutorial](http://www.webartisan.ch/en/products/soundy-free/#wa_tutorial), [FAQ](http://www.webartisan.ch/en/products/soundy-free/#wa_FAQ), [Examples](http://www.webartisan.ch/en/products/soundy-free/#wa_examples) and more detailed information about **Soundy** plugin on [WebArtisan.ch](http://www.webartisan.ch/en/products/soundy-free/). If you were unable to find the answer to your question on the FAQ or in any of the documentation, you should check [Soundy's Support Forum](http://wordpress.org/support/plugin/soundy-background-music) on WordPress.org. If you can't locate any topics that pertain to your particular issue, post a new topic for it.  
**Soundy** Plugin Home Page: [www.webartisan.ch/en/products/soundy-free](http://www.webartisan.ch/en/products/soundy-free/)

== Installation ==

* In the Admin area (the back-end) of your WordPress Web Site, go to Plugins > Add New.
* Enter **Soundy** in the search field.
* **Soundy Background Music** appears.
* Click on "Install Now".
* Click on "Activate Plugin".
* To let a page or post to play a soundtrack:

1. Go into the *Edit Page* or *Edit Post* tool of this page in the Admin area.
1. Set the option *Enable Background Sound* to *Yes* in the Soundy meta box and update the page.
1. The page will then play the default soundtrack when displayed.

* To set up plugin defaults, go to Settings > **Soundy** in the admin area and fill out the input fields.
* For help, here is a [tutorial](http://www.webartisan.ch/en/products/soundy-free/#wa_tutorial).
* Happy Soundy Music !

= Updates =

* After an update of Soundy you must clear the cache of your browser for the Settings > Soundy page as well as the Edit Post and Edit Page pages. This is because cached Javascript and CSS files are modified at each Soundy update.

== Frequently Asked Questions ==

1. **Why is Soundy Background Music a Must on my WordPress Website ?**  
Having a home page or any other page playing a background music greatly improves your website visitor's experience. By choosing a music in adequacy with your content, Soundy will help making your visitors immersed in your pages. Pages with a slideshow are excellent candidates for Soundy. But a lot of other page types too.

1. **Is it possible to have different soundtracks for different posts and pages ?**  
Yes, a soundtrack can be set up by authors on a per post or per page basis.

1. **What kind of audio files can be used with Soundy Plugin ?**  
The audio files must be in the MP3, OGG or WAV format. The file extension must be .mp3, .mpg, .mpeg, .ogg .wav or .wave.

1. **Is Soundy Plugin compatible with iphone, ipad and smartphones in general ?**  
Yes it is, as the plugin uses the HTML5 audio tag. There is just one issue with ipod, iphone and ipad (IOS Operating System): Autoplay option is deactivated by Apple on this operating system.

1. **Is it possible to position the Play/Pause button anywhere in the page or post header ?**  
Yes, this can be done with the soundy_button() template tag.

1. **Is it possible to position the Play/Pause button anywhere in the content of a page or post ?**  
Yes, this can be done with the [soundy button] shortcode.

1. **Is it possible to modify the Play/Pause button look & feel ?**  
Yes, you can upload and set up your own button images.

1. **Can I really put my audio files anywhere on the web ?**  
Yes, you can put your audio files anywhere on any cloud or website, except one place: you should not put your audio files in the soundy-background-music plugin directory (or below). If you do, you will loose your files the next time you will install a new version of Soundy. The update process deletes this folder and replaces it with a new one. If you want to store the audio files on your WordPress site, the best place to do so is the media library (/wp-content/uploads/...).

1. **After an update of Soundy, my soundtracks are not played anymore and my custom Play/Pause button images are broken. What happened ?**  
As mentioned in the previous paragraph, the reason might be that you had uploaded your audio files and button images under the soundy-background-music plugin directory. This directory is erased and replaced at each update. Do not add any file in it. Again, the prefered location for such files is the WordPress media library.

1. **How should I proceed to enable Soundy Background Music only for a few pages or posts ?**  
Go into Soundy's settings page (Settings > Soundy) and uncheck the checkbox “Enable Background Sound”. It actually is unchecked per default. This way Soundy won’t be enabled per default on your site. Then for each page or post in which you want to enable Soundy, go into the Edit Page admin tool and set the “Enable Background Sound” option to “Yes” by checking the corresponding radio button.

1. **I've changed the default soundtrack in the Soundy administration (Settings > Soundy) with a new sountrack but a page carries on playing the old soundtrack. What's happening ?**  
While the old soundtrack was active an author did change the Soundtrack option to *Custom* in the Edit Page Soundy meta box.  
To correct the problem, reset the Soundtrack option of the Soundy meta box back to *Default* and update the page.
In *Custom* mode a page retains the soundtrack defined when the *Custom* option was selected. In *Default* mode, the page uses the default soundtrack currently defined in the Soundy settings.

1. **Is it advised to put my audio files on a separate storage cloud ?**  
Yes it is, especially if you have a lot of traffic on your website. Putting your audio files on a separate storage cloud will release the load on your website and improve page load response time in your visitor's browsers. There is a lot of Cloud Storage Providers out there like Dropbox, Google Drive, SkyDrive and many others. Your hosting provider might also offer cloud storage.

1. **Does Soundy allow having a continuous uninterrupted audio stream playing while navigating on my site ?**  
Because most of the WP themes create and download a new HTML document in your browser each time you navigate to another WP post or page, the answer is NO for such themes.  
However there could be themes which don't download a new HTML document when you switch to another WP post or page. They would rather use Ajax to download the WP post/page in the content area of the downloaded unique main site HTML document.
If such themes exist, then, YES, by using such a theme you could have a continuous uninterrupted audio stream playing while navigating on your site.  
A Soundy's user is using the [Premium Crea WP theme](http://themeforest.net/item/crea-wp/424783) on his site: http://www.hanstylewedding.com/  
While navigating in the bottom menu of this site, the audio stream is not interrupted and remain continuous as the theme doesn't download any new HTML document while navigating in this menu.  
Having a continuous uninterrupted audio stream playing while navigating on a WP site with Soundy plugin might be possible with some themes but on most themes, it is impossible.  
Here, at [WebArtisan.ch](http://www.webartisan.ch/), we are considering developing a theme which will allow this valuable feature. This will be the Soundy Theme.  
If you know of other themes like the Crea WP theme which don't download new HTML documents while navigating on the site, please, let us know !

1. **On WP front-end, Soundy's Play/Pause button does not respond correctly. What's the problem ?**  
Soundy needs jQuery 1.10.2 which is the default jQuery library of the last versions of WordPress. However some themes load their own jQuery library. If this library is not up-to-date, Soundy's Play/Pause button gets into troubles.  
Using WordPress Default jQuery library is actually what Soundy does with the following statement in soundy.php:  
`wp_register_script( 'soundy-front-end', $this->plugin_url . '/js/front-end.js', array( 'jquery' ) );  
wp_enqueue_script( 'soundy-front-end' );` 
array( ‘jquery’) is an argument to wp_register_script saying that front-end.js depends on WordPress default jQuery library.  
And this is what your theme also should do instead of loading its own old jQuery version.

== Screenshots ==

1. **Default Audio Track Settings**
1. **Play/Pause Button Settings**
1. **Play/Pause Button Corner Settings**
1. **Play/Pause Button Static Settings**
1. **Page or Post Soundy Metabox**

== Changelog ==

= 2.0 =
* Object Oriented Redesign of the whole plugin.
* Added Play/Pause Button Preview in Context of any Page or Post in Settings > Soundy > Play/Pause Button Tab.
* Added Play/Pause Button Preview in Context of any Page or Post in Settings > Soundy > Play/Pause Corner Position Tab.
* Added Swap Button in Settings > Soundy > Play/Pause Button Tab to swap the URLs of the normal and hover images.
* Fixed minor bug in New Page and New Post Soundy metabox.
* **[Soundy PRO](http://www.webartisan.ch/en/products/soundy-pro/) and its HTML5 Play/Pause Button Designer application is now available.**

= 1.2 =
* Added Audio Volume Control for administrators in Settings > Soundy.
* Added Audio Volume Control for authors in Edit Page and Edit Post Soundy meta box.
* Made Admin User Interface more responsive and user friendlier.

= 1.1 =
* Corrected a few minor bugs
* Made Soundy forms of the back-end admin section more responsive
* Added possibility to disable Soundy for mobile devices. Set variable **$disable_soundy_for_mobile** to **true** in soundy.php file to disable Soundy for mobile devices.

= 1.0 =
* First Version

== Upgrade Notice ==

= 2.0 =
* After having upgraded Soundy to 1.2, please clear the cache of Soundy's admin pages in your browser. The reason is that a few CSS and Javascript files have been modified and these files are typically cached by browsers.
* [Soundy PRO](http://www.webartisan.ch/en/products/soundy-pro/) and its HTML5 Play/Pause Button Designer application is now available.

= 1.2 =
* After having upgraded Soundy to 1.2, please clear the cache of Soundy's admin pages in your browser. The reason is that a few CSS and Javascript files have been modified and these files are typically cached by browsers.

= 1.1 =
* After having upgraded Soundy to 1.1, please clear the cache of Soundy's admin pages in your browser. The reason is that a few CSS and Javascript files have been modified and these files are typically cached by browsers.