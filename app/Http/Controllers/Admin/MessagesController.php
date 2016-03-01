<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends Controller {

    public function index()
    {
        $messages = include(app_path('../resources/lang/en/validation.php'));
        return response()->view('views.messages.index', ['messages' => $messages['custom']]);
    }

    public function save(Request $request)
    {
        $messages = include(app_path('../resources/lang/en/validation.php'));
        $all = $request->all();
        foreach ($all as $attribute => $message) {
            $exploded = explode("/", $attribute);
            $messages['custom'][$exploded[0]][$exploded[1]] = $message;
        }

        $h = fopen(app_path('../resources/lang/en/validation.php'), 'w');
        fwrite($h, '<?php return ');
        fwrite($h, var_export($messages, true));
        fwrite($h, ';');
        fclose($h);

        $headers = ["Expires" => gmdate("D, d M Y H:i:s", time()) . " GMT",
            "Last-Modified" => gmdate("D, d M Y H:i:s") . " GMT",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache"];

        return redirect(route('success'), 302, $headers);
    }

    public function show()
    {
        $headers = ["Expires" => gmdate("D, d M Y H:i:s", time()) . " GMT",
            "Last-Modified" => gmdate("D, d M Y H:i:s") . " GMT",
            "Cache-Control" => "no-cache, must-revalidate",
            "Pragma" => "no-cache"];

        $messages = require app_path('../resources/lang/en/validation.php');
        return response()->view('views.messages.show', ['messages' => $messages['custom']], 302, $headers);
    }

}
