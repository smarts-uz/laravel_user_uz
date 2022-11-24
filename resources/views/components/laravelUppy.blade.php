<link href="https://releases.transloadit.com/uppy/v2.4.1/uppy.min.css" rel="stylesheet">
<script src="https://releases.transloadit.com/uppy/v2.4.1/uppy.min.js"></script>
<script src="https://releases.transloadit.com/uppy/v2.4.1/uppy.legacy.min.js" nomodule></script>
<script src="https://releases.transloadit.com/uppy/locales/v3.0.4/ru_RU.min.js"></script>
<script src="https://releases.transloadit.com/uppy/locales/v3.0.4/uz_UZ.min.js"></script>
<script>
    var uppy = new Uppy.Core()
        .use(Uppy.Dashboard, {
            trigger: '.UppyModalOpenerBtn',
            inline: true,
            target: '#photos',
            showProgressDetails: true,
            allowedFileTypes: ['image/*'],
            debug: true,
            @if(session('lang') === 'ru')
                locale: Uppy.locales.ru_RU,
            @else
                locale: Uppy.locales.uz_UZ,
            @endif

            note: 'Все типы файлов, до 10 МБ',
            height: 400,
            metaFields: [
                {id: 'name', name: 'Name', placeholder: 'file name'},
                {id: 'caption', name: 'Caption', placeholder: 'describe what the image is about'}
            ],
            browserBackButtonClose: true
        })

        .use(Uppy.ImageEditor, {target: Uppy.Dashboard})
        .use(Uppy.XHRUpload, {
            endpoint: '{{$route}}',
            fieldName: 'images[]',
            method: 'post',
            bundle: true,
        });

    uppy.on('upload-success', (file, response) => {
        const httpStatus = response.status // HTTP status code
        const httpBody = response.body   // extracted response data

    });


    uppy.on('file-added', (file) => {
        uppy.setFileMeta(file.id, {
            size: file.size,

        })
        console.log(file.name);
    });
    uppy.on('complete', result => {
        console.log('successful files:', result.successful)
    });
</script>

