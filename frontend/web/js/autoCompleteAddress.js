(function () {
    const autoCompleteJS = new autoComplete({
        wrapper: false,
        threshold: 5,
        debounce: 600,
        searchEngine: 'loose',
        data: {
            src: async () => {
                try {
                    const source = await fetch("/location?" + new URLSearchParams({search: autoCompleteJS.input.value}));
                    return await source.json();
                } catch (error) {
                    return error;
                }
            },
            keys: ["full_address"],
            cache: false,
        },
        resultsList: {
            element: (list, data) => {
                const info = document.createElement("p");
                if (data.results.length > 0) {
                    info.innerHTML = `Показано <strong>${data.results.length}</strong> из <strong>${data.matches.length}</strong> результатов`;
                } else {
                    info.innerHTML = `Найдено <strong>${data.matches.length}</strong> подходящих результатов <strong>"${data.query}"</strong>`;
                    autoCompleteJS.input.addEventListener('blur', function () {
                        autoCompleteJS.input.value = null;
                    })
                }
                list.prepend(info);
            },
            noResults: true,
            maxResults: 20,
            tabSelect: true
        },
        resultItem: {
            element: (item, data) => {
                // Modify Results Item Style
                item.style = "display: flex; justify-content: space-between;";
                // Modify Results Item Content
                item.innerHTML = `
      <span style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
        ${data.match}
      </span>`;
            },
            highlight: true
        },
        events: {
            input: {
                focus: () => {
                    if (autoCompleteJS.input.value.length) autoCompleteJS.start();
                }
            }
        }
    });


    autoCompleteJS.input.addEventListener("selection", function (event) {
        const feedback = event.detail;
        autoCompleteJS.input.blur();
        // Prepare User's Selected Value
        const selection = feedback.selection.value[feedback.selection.key];
        // Replace Input value with the selected value
        autoCompleteJS.input.value = selection;
        const latitude = document.getElementById('task-latitude');
        latitude.value = feedback.selection.value.latitude;
        const longitude = document.getElementById('task-longitude');
        longitude.value = feedback.selection.value.longitude;
        const city = document.getElementById('city-name');
        city.value = feedback.selection.value.city;
        const address = document.getElementById('task-address');
        address.value = feedback.selection.value.short_address;
    });

})();