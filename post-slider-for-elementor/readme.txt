=== Post slider elementor addons ===
Contributors: solverwp
Donate link: https://solverwp.com/
Tags: elementor,post slider, slider, Recent Post, post
Requires PHP: 7.4
Requires at least: 6.0
Tested up to: 7.0
Elementor tested up to: 3.26.0
Stable tag: 2.2.1
Version: 2.2.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
A beautiful, easy to use post slider widget for Elementor to display your recent posts.

Post slider elementor addons is very beautiful slider to display recent post. It's very easy to use and totally dynamic. It's a elementor addon. So to use this plugin you need to install first elementor page builder.


***Features***

1. Drag And Drop
2. Beautiful design
3. nav icon
4. left/right icon
5. Typography Opion
6. Color Option
7.Easy to use
8. Display Recent Post
9. Category filter
10. tag filter
11. ordering




How to use:-
very easy to use, install psea and elementor plugin . Then darg and drop psea


== Installation ==

**The easy way.**

Go to your WordPress Dashboard. Navigate to Plugins > Add New and then search for "psea". Click on Install and then Activate the Plugin.

That's it, psea Plugin is now activated on your site!

**The hard way..**

Download "psea Plugin"  and then extract the zip file and just drop the contents in the wp-content/plugins/ directory of your WordPress installation and then activate the Plugin from Plugins page.





== Frequently Asked Questions ==
=  Is this plugin compatable on latest wordpress and elementor version ?  =
Yes

== Screenshots ==

1.  /assets/screenshot-1.png
2.  /assets/screenshot-2.png
3.  /assets/screenshot-3.png
4.  /assets/screenshot-4.png
5.  /assets/screenshot-5.png
6.  /assets/screenshot-6.png
6.  /assets/screenshot-7.png


== Changelog ==

= 2.2.0 =
* New: the plugin is now a full widget pack — 8 new Elementor widgets added alongside the original Post Slider: Post Grid, Post Masonry, Post List, Post Category Slider, Recent Viewed Posts, Related Post Slider, Ticker List, and Timeline.
* New: Widget Manager settings page (Post Slider menu) — switch each widget on/off individually; disabled widgets are not loaded at all.
* New: Recent Viewed Posts tracks each visitor's opened posts via a lightweight cookie (no accounts, no external service).
* New: shared widget styles/behaviour load only for widgets that use them, via Elementor script/style dependencies (works in the editor preview too).

= 2.1.1 =
* Fixed: the slider now renders inside the Elementor EDITOR preview, not just the frontend. The slider script/style are declared as widget dependencies (get_script_depends/get_style_depends) so Elementor loads them in the editor iframe, and the init script re-runs correctly on editor re-renders.
* Fixed: each slider instance now gets a unique id — the old hardcoded #demo1 broke pages containing two Post Slider widgets.
* Fixed: the "Posts to show" field was a free-text control, which could hold an empty/non-numeric value and make the widget's post query return zero results — most noticeable as a blank box in the Elementor editor. It's now a proper Number field with a safe default.
* Fixed: excerpts now use get_the_excerpt() instead of get_the_content() — calling get_the_content() from inside a widget that Elementor is itself rendering as part of the page's content can hit Elementor's own recursion guard and return empty, which is far more likely to happen in the editor preview than on the frontend.
* Fixed: when a category/tag filter matches no posts, the widget now shows a small "No posts found" message instead of an empty box, and no longer tries to initialize the slider (which previously threw a JS error) with zero slides.
* Fixed: the second get_terms() call (for the tag filter) still had the old, incorrect posts_per_page argument.

= 2.1.0 =
* Compatibility update for WordPress 7.0 and the latest Elementor.
* Fixed: widget registration now uses Elementor's current elementor/widgets/register hook and register() method (the old register_widget_type() API was removed from recent Elementor versions and would fatal on activation).
* Fixed: removed all use of Elementor's Scheme_Color / Scheme_Typography classes, which were removed from Elementor years ago in favor of Global Colors/Fonts and would fatal the widget's style controls.
* Fixed: renamed _register_controls() to register_controls() to match Elementor's current widget API.
* Fixed: a potential fatal error on sites where the plugin's install-date option was never set (used for the review-request notice).
* Housekeeping: synced version numbers across the plugin and enqueued assets with cache-busting versioning.

=2.0.0=
*category filter added
*tag filter added

= 1.0 =
*Initail Rpseaase

== Upgrade Notice ==

= 1.0 =
This is initial Rpseaase for elementor post slider