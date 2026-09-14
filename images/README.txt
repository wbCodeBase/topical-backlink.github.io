IMAGES
======

Every <img> on the site currently points at one of the ph-*.svg placeholders in
this folder. To use a real photo:

  1. Drop your file in this folder (e.g. team-outreach.jpg).
  2. Change that img's src from images/ph-wide.svg to images/team-outreach.jpg.
  3. Update the alt text to describe the actual photo.

The small "PLACEHOLDER" badge on each image hides itself automatically as soon
as the src no longer points at a ph-*.svg file - nothing to remember to remove.

Keep the width/height attributes on the <img> roughly matching your real image's
aspect ratio so the page does not shift while images load.

Recommended sizes
-----------------
  ph-wide.svg      1200 x 675   service rows, case covers, story image
  ph-4x3.svg       1200 x 900   general content blocks
  ph-square.svg     900 x 900   gallery tiles
  ph-portrait.svg   900 x 1200  team portraits
  ph-avatar.svg     400 x 400   testimonial / team headshots
  ph-wide-sm.svg    800 x 450   smaller inline media
