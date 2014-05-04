Pygments content element
========================

[Pygments](http://pygments.org/) - the Python syntax highlighter - is a nice syntax highlighter with a lot of lexers,
formatters and styles. This extension provide a content element and frontend module that output highlighted code made
with pygments.

Limitations
-----------

You can select a style per element. This is useful if you use different styles on different pages. But keep in mind
that you never can use multiple styles on the same page!

Optimisation
------------

If you select a style in the content element or frontend module, the required style will be added to the page. If you
want to use a "global" style definition, do not select any style in the element. You can dump the styles with
`pygmentize -f html -S <style>`. In most cases `pygmentize -f html -S default` is a good choice. Use `pygmentize -L
styles` to get a complete list of all available styles.

After you dumped the styles, put them into a css file and include these file in your layout.

Tip: use the style selector in the element to find your favorite style.
