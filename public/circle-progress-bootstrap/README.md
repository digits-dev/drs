# jquery-bs-circle-progress

The plugin displays a process in the form of a circle using Bootstrap and jQuery.

## Requirements

- Bootstrap >= v4.0.0 (Works with Bootstrap 5.x)
- jQuery >= 1.9

## Installing

### Manual

Download the compressed file jquery-bs-circle-progress.min.js from the dist folder.
Upload it to your project and include it before the </body> tag but after the jQuery script.

```html

<script src="path/to/jquery/jquery.min.js"></script>
<script src="path/to/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="path/to/jquery-bs-circle-progress.min.js"></script>
<script>
    // custom scripts
</script>
</body>
```

### Manual

```shell
composer require webcito/jquery-bs-circle-progress:dev-main
```

### Usage

#### html

```html

<div id="my_first_progress"></div>
```

#### javascript

```js
 $('#my_first_progress').circleProgress();
```

That's it!

## Plugin options

| prop          | type   | default       | description                                                                                                                |
|---------------|--------|---------------|----------------------------------------------------------------------------------------------------------------------------|
| size          | number | 200           | The size of the circle                                                                                                     |
| value         | number | 0             | The predefined value of progress                                                                                           |             
| color         | string | 'primary'     | The color of the progress. It can be a bootstrap class ('primary', 'secondary', etc.) or a CSS property (rgb(20,20,20)).   |
| background    | string | 'transparent' | The color of the background. It can be a bootstrap class ('primary', 'secondary', etc.) or a CSS property (rgb(20,20,20)). |
| progressWidth | number | null          | The thickness of the progress bar. If no value is specified, the thickness is calculated automatically using the circle.   |

### example

```js
 $('selector').circleProgress({
    size: 350,
    value: 12,
    color: '#506886'
});
```

## Plugin methods

| method        | params | description                                |
|---------------|--------|--------------------------------------------|
| val           | number | Changes the value of progress              |
| updateOptions | object | Rebuilds the plugin using the new options. |

### example

```js
 $('selector').circleProgress({
    size: 350,
    value: 12,
    color: '#506886'
});

let seconds = 0;
let testInterval = null;
testInterval = setInterval(function () {
    if (seconds === 100) {
        clearInterval(testInterval)
    }
    let color;
    switch (true) {
        case seconds < 20:
            color = 'danger';
            break;
        case seconds < 40:
            color = 'warning';
            break;
        case seconds < 60:
            color = 'info';
            break;
        case seconds < 80:
            color = 'primary';
            break;
        default:
            color = 'success';
            break;
    }
    $('selector').circleProgress('updateOptions', {
        value: seconds,
        color: color
    });
    seconds++;
}, 100)
```

