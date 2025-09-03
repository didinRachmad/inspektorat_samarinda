import AutoNumeric from "autonumeric";

export function initAutoNumeric(selector = "input.numeric") {
    document.querySelectorAll(selector).forEach((el) => {
        const instance = AutoNumeric.getAutoNumericElement(el);
        if (instance) instance.remove(); // aman re-init
        new AutoNumeric(el, {
            digitGroupSeparator: ".",
            decimalCharacter: ",",
            decimalPlaces: 0,
            unformatOnSubmit: true,
            modifyValueOnWheel: false,
            selectNumberOnlyOnFocus: true,
        });
    });
}
