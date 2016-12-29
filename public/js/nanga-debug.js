(function ($) {
    $(window).load(function () {
        //console.log(vg_app);
        //console.log(vg_contact_form);
        //console.log(vg_newsletter_form);
        //console.log(vg_map);
    });
})(jQuery);
HTMLInspector.inspect({
    domRoot: "body",
    excludeRules: ["inline-event-handlers", "script-placement", "unused-classes"],
    excludeElements: ["svg", "iframe", "form", "input", "textarea", "font"]
});
