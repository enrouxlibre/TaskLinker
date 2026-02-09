let id = 1;

document.querySelectorAll("select[multiple]").forEach(function (select) {
    let new_id = id++;
    const div = document.createElement("div");
    div.classList.add("select-div");
    div.style.anchorName = "--div" + new_id;
    const infobox = document.createElement("div");
    infobox.classList.add("infobox");
    infobox.style.positionAnchor = "--div" + new_id;
    let checkbox_id = 0;
    select.querySelectorAll("option").forEach(function (option) {
        let c_id = checkbox_id++;
        const checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.id = "checkbox_" + new_id + "_" + c_id;
        option.dataset.cid = checkbox.id;
        checkbox.checked = option.selected;
        const label = document.createElement("label");
        label.htmlFor = checkbox.id = "checkbox_" + new_id + "_" + c_id;
        label.textContent = option.textContent;
        checkbox.addEventListener("change", function () {
            option.selected = checkbox.checked;
            show_selected(div, select);
        });
        infobox.append(checkbox);
        infobox.append(label);
    });
    const infoboxInput = document.createElement("input");
    infoboxInput.classList.add("infobox-input");
    infoboxInput.addEventListener("keyup", function (e) {
        if (e.key === "Escape") {
            infoboxInput.value = "";
        }
        const search = infoboxInput.value.toLowerCase();
        filterInfobox(infobox, search);
    });
    infobox.prepend(infoboxInput);
    select.before(infobox);
    show_selected(div, select);
    select.before(div);
    select.addEventListener("change", function () {
        show_selected(div, select);
    });
    select.classList.add("hidden");
    infobox.classList.add("hidden");
    div.addEventListener("click", function () {
        infobox.classList.remove("hidden");
        infoboxInput.focus();
    });
    document.addEventListener("click", function (event) {
        if (
            !div.contains(event.target) &&
            !infobox.contains(event.target) &&
            !event.target.classList.contains("delete-div")
        ) {
            infoboxInput.value = "";
            filterInfobox(infobox, "");
            infobox.classList.add("hidden");
        } else {
            infoboxInput.focus();
        }
    });
});

function show_selected(div, select) {
    div.innerHTML = "";
    select.querySelectorAll("option").forEach(function (option) {
        if (!option.selected) {
            return;
        }
        const optionDiv = document.createElement("div");
        optionDiv.classList.add("option-div");
        optionDiv.textContent = option.textContent;
        const deleteDiv = document.createElement("div");
        deleteDiv.classList.add("delete-div");
        deleteDiv.textContent = "X";
        deleteDiv.addEventListener("click", function () {
            option.selected = false;
            show_selected(div, select);
            document.getElementById(option.dataset.cid).checked = false;
        });
        optionDiv.append(deleteDiv);
        div.append(optionDiv);
    });
}

function filterInfobox(infobox, search) {
    infobox.querySelectorAll("label").forEach((l) => {
        const text = l.textContent.toLowerCase();
        if (text.includes(search)) {
            l.classList.remove("hidden");
            document.getElementById(l.htmlFor).classList.remove("hidden");
        } else {
            l.classList.add("hidden");
            document.getElementById(l.htmlFor).classList.add("hidden");
        }
    });
}
