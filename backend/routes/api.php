<?php

use App\Models\Game;
use App\Models\User;
use App\Models\Group;
use App\Models\Player;
use App\Models\UserGroup;
use App\Models\MessageGroup;
use Illuminate\Http\Request;
use App\Models\MessageUser;
use App\Models\UserGameRole;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

function logOut($text){
    $out = new \Symfony\Component\Console\Output\ConsoleOutput();
    $out->writeln($text);
}

function datetimeCheck ($val, $format = 'Y-m-d H:i:s') {
    $d = DateTime::createFromFormat($format, $val);
    return (integer)($d && $d->format($format) == $val);
}

Route::post('/user/create', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (strlen($request->input('login')) < 5) {
        return json_encode(["status"=>"error", "description"=>"Login must be longer than 5 chars"]);
    }
    if (strlen($request->input('login')) > 50) {
        return json_encode(["status"=>"error", "description"=>"Login must be shorter than 50 chars"]);
    }
    if (count(User::where("login", $request->input('login'))->get()) > 0) {
        return json_encode(["status"=>"error", "description"=>"Login is already taken"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    if (strlen($request->input('password')) < 5) {
        return json_encode(["status"=>"error", "description"=>"Password must be longer than 5 chars"]);
    }
    if (strlen($request->input('password')) > 50) {
        return json_encode(["status"=>"error", "description"=>"Password must be shorter than 50 chars"]);
    }
    if (User::where('login', $request->input('login'))->count() > 0) {
        return json_encode(["status"=>"error", "description"=>"Login is already taken"]);
    }
    $user = new User;
    $user->login = $request->input('login');
    $user->password = hash('sha512', $request->input('password'));
    $user->avatar = "";
    $user->save();
    return json_encode(["status"=>"ok"]);
});

Route::post('/user/check', function(Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    return json_encode(["status"=>"ok"]);
});

Route::post('user/change/avatar', function (Request $request) {
    logOut("Przynajmniej endpoint jakoś działa");
    logOut($request->file("avatar"));
    logOut($request->input("login"));
    logOut($request->input("password"));

    if (!($request->input('login') !== "")) {
        logOut("1");
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!($request->input('password') !== "")) {
        logOut("2");
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    logOut("3");
    if (!$request->hasFile('avatar')) {
        return json_encode(["status"=>"error", "description"=>"File is required"]);
    }
    if (!$request->file('avatar')->isValid()) {
        return json_encode(["status"=>"error", "description"=>"Upload unsuccessful"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!(($request->file('avatar')->getMimeType() == "image/png") || ($request->file('avatar')->getMimeType() == "image/jpeg"))) {
        return json_encode(["status"=>"error", "description"=>"Required format is jpeg or png"]);
    }
    if ($request->file('avatar')->getSize() > 4294967296) {
        return json_encode(["status"=>"error", "description"=>"File is bigger than 4GB"]);
    }
    $filename = $request->file('avatar')->store("img");
    if (($user->avatar != "") && (Storage::exists($user->avatar))) {
        Storage::delete($user->avatar);
    }
    $user->avatar = $filename;
    $user->save();
    return json_encode(["status"=>"ok"]);
});

Route::post('user/change/login', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('newLogin')) {
        return json_encode(["status"=>"error", "description"=>"New login is required"]);
    }
    if (strlen($request->input('newLogin')) < 5) {
        return json_encode(["status"=>"error", "description"=>"New login must be longer than 5 chars"]);
    }
    if (strlen($request->input('newLogin')) > 50) {
        return json_encode(["status"=>"error", "description"=>"New login must be shorter than 50 chars"]);
    }
    if (count(User::where("login", $request->input('newLogin'))->get()) > 0) {
        return json_encode(["status"=>"error", "description"=>"New login is already taken"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if(empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    $user->login = $request->input('newLogin');
    $user->save();
    return json_encode(["status"=>"ok"]);
});

Route::post('user/change/password', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    if (!$request->has('newPassword')) {
        return json_encode(["status"=>"error", "description"=>"New password is required"]);
    }
    if (strlen($request->input('newPassword')) < 5) {
        return json_encode(["status"=>"error", "description"=>"New password must be longer than 5 chars"]);
    }
    if (strlen($request->input('newPassword')) > 50) {
        return json_encode(["status"=>"error", "description"=>"New password must be shorter than 50 chars"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if(empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    $user->password = hash('sha512', $request->input('newPassword'));
    $user->save();
    return json_encode(["status"=>"ok"]);
});

Route::post('/game/list', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if(empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    $answer = [];
    $games = Game::all();
    for ($i = 0; $i < count($games); $i++) {
        $temp["id"] = $games[$i]["id"];
        $temp["name"] = $games[$i]["name"];
        $temp["sport"] = $games[$i]["sport"];
        $temp["advancement"] = $games[$i]["advancement"];
        $temp["location"] = $games[$i]["location"];
        $temp["time"] = $games[$i]["time"];
        $temp["price"] = $games[$i]["price"];
        $temp["people_counter"] = $games[$i]["people_counter"];
        $temp["users"] = [];
        $userGameRole = UserGameRole::where("game", $games[$i]["id"])->get();
        for ($j = 0; $j < count($userGameRole); $j++) {
            $temp["users"][$j] = (User::where("id", $userGameRole[$j]->user) -> first())["login"];
        }
        $answer[$i] = $temp;
    }
    return json_encode(["status"=>"ok", "data"=>$answer]);
});

Route::post('/game/get', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if(empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('id')) {
        return json_encode(["status"=>"error", "description"=>"Id is required"]);
    }
    if (!is_numeric($request->input('id'))) {
        return json_encode(["status"=>"error", "description"=>"Id isn't number"]);
    }
    $game = Game::find($request->input('id'));
    if(empty($game)) {
        return json_encode(["status"=>"error", "description"=>"Game isn't exist"]);
    }
    $answer = [];
    $answer["id"] = $game["id"];
    $answer["name"] = $game["name"];
    $answer["sport"] = $game["sport"];
    $answer["advancement"] = $game["advancement"];
    $answer["location"] = $game["location"];
    $answer["time"] = $game["time"];
    $answer["price"] = $game["price"];
    $answer["people_counter"] = $game["people_counter"];
    $answer["users"] = [];
    $userGameRole = UserGameRole::where("game", $game["id"])->get();
    for ($j = 0; $j < count($userGameRole); $j++) {
        $answer["users"][$j] = (User::where("id", $userGameRole[$j]->user) -> first())["login"];
    }
    return json_encode(["status"=>"ok", "data"=>($answer)]);
});

Route::post('/game/create', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('name')) {
        return json_encode(["status"=>"error", "description"=>"Name is required"]);
    }
    if (strlen($request->input('name')) < 5) {
        return json_encode(["status"=>"error", "description"=>"Name must be longer than 5 chars"]);
    }
    if (strlen($request->input('name')) > 200) {
        return json_encode(["status"=>"error", "description"=>"Name must be shorter than 200 chars"]);
    }
    if (!$request->has('sport')) {
        return json_encode(["status"=>"error", "description"=>"Sport is required"]);
    }
    if (strlen($request->input('sport')) < 5) {
        return json_encode(["status"=>"error", "description"=>"Sport must be longer than 5 chars"]);
    }
    if (strlen($request->input('sport')) > 200) {
        return json_encode(["status"=>"error", "description"=>"Sport must be shorter than 200 chars"]);
    }
    if (!$request->has('advancement')) {
        return json_encode(["status"=>"error", "description"=>"Advancement is required"]);
    }
    if (!is_numeric($request->input('advancement'))) {
        return json_encode(["status"=>"error", "description"=>"Advancement must be a number in the range of 0 to 10"]);
    }
    if (strlen($request->input('advancement')) < 0) {
        return json_encode(["status"=>"error", "description"=>"Advancement must be in the range of 0 to 10"]);
    }
    if (strlen($request->input('advancement')) > 10) {
        return json_encode(["status"=>"error", "description"=>"Advancement must be in the range of 0 to 10"]);
    }
    if (!$request->has('location')) {
        return json_encode(["status"=>"error", "description"=>"Location is required"]);
    }
    if (strlen($request->input('location')) < 10) {
        return json_encode(["status"=>"error", "description"=>"Location must be longer than 10 chars"]);
    }
    if (strlen($request->input('location')) > 500) {
        return json_encode(["status"=>"error", "description"=>"Location must be shorter than 500 chars"]);
    }
    if (!$request->has('time')) {
        return json_encode(["status"=>"error", "description"=>"Time is required"]);
    }
    if (!datetimeCheck($request->input('time'))) {
        return json_encode(["status"=>"error", "description"=>"Time format is invalid"]);
    }
    if (!$request->has('price')) {
        return json_encode(["status"=>"error", "description"=>"Price is required"]);
    }
    if(!is_numeric($request->input('price'))) {
        return json_encode(["status"=>"error", "description"=>"Price must be a number"]);
    }
    if ($request->input('price') < 0) {
        return json_encode(["status"=>"error", "description"=>"Price can't be a negative number"]);
    }
    if (!$request->has('people_counter')) {
        return json_encode(["status"=>"error", "description"=>"People_counter is required"]);
    }
    if(!is_numeric($request->input('people_counter'))) {
        return json_encode(["status"=>"error", "description"=>"People_counter must be a number in the range of 0 to 10"]);
    }
    if ($request->input('people_counter') < 0) {
        return json_encode(["status"=>"error", "description"=>"Advancement must be in the range of 1 to 40"]);
    }
    if ($request->input('people_counter') > 40) {
        return json_encode(["status"=>"error", "description"=>"Advancement must be in the range of 1 to 40"]);
    }
    $game = new Game;
    $game->name = $request->input('name');
    $game->sport = $request->input('sport');
    $game->advancement = (int)$request->input('advancement');
    $game->location = $request->input('location');
    $game->time = $request->input('time');
    $game->price = $request->input('price');
    $game->people_counter = $request->input('people_counter');
    $game->save();
    $UserGameRole = new UserGameRole;
    $UserGameRole->user = $user->id;
    $UserGameRole->game = $game->id;
    $UserGameRole->role = "właściciel";
    $UserGameRole->save();
    return json_encode(["status"=>"ok"]);
});

Route::post('/game/join', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('id')) {
        return json_encode(["status"=>"error", "description"=>"Id is required"]);
    }
    if (!is_numeric($request->input('id'))) {
        return json_encode(["status"=>"error", "description"=>"Id isn't number"]);
    }
    $game = Game::find($request->input('id'));
    if(empty($game)) {
        return json_encode(["status"=>"error", "description"=>"Game isn't exist"]);
    }
    $participant = $UserGameRole::where('user', $user->id)->first();
    if (!empty($participant)) {
        return json_encode(["status"=>"error", "description"=>"You are already a participant in this course"]);
    }
    $UserGameRole = new UserGameRole;
    $UserGameRole->user = $user->id;
    $UserGameRole->game = $game->id;
    $UserGameRole->role = "uczestnik";
    $UserGameRole->save();
    return json_encode(["status"=>"ok"]);
});

Route::post('/game/join', function (Request $request) {
    if (!$request->has('login')) {
        return json_encode(["status"=>"error", "description"=>"Login is required"]);
    }
    if (!$request->has('password')) {
        return json_encode(["status"=>"error", "description"=>"Password is required"]);
    }
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('id')) {
        return json_encode(["status"=>"error", "description"=>"Id is required"]);
    }
    if (!is_numeric($request->input('id'))) {
        return json_encode(["status"=>"error", "description"=>"Id isn't number"]);
    }
    $game = Game::find($request->input('id'));
    if(empty($game)) {
        return json_encode(["status"=>"error", "description"=>"Game isn't exist"]);
    }
    $participant = $UserGameRole::where('user', $user->id)->first();
    if (empty($participant)) {
        return json_encode(["status"=>"error", "description"=>"You aren't a participant in this course"]);
    }
    $participant->delete();
    return json_encode(["status"=>"ok"]);
});

Route::post('/group/list', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    return json_encode(["status"=>"ok", "data"=>(UserGroup::select("group")->distinct()->get())]);
});

Route::post('/group/create', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('name')) {
        return json_encode(["status"=>"error", "description"=>"Name is required"]);
    }
    if (strlen($request->input('name')) < 4) {
        return json_encode(["status"=>"error", "description"=>"Name must be longer than 3 chars"]);
    }
    if (strlen($request->input('name')) > 200) {
        return json_encode(["status"=>"error", "description"=>"Name must be shorter than 200 chars"]);
    }
    $userGroup = new UserGroup;
    return json_encode(["status"=>"ok"]);
});

Route::post('/message/user/list', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    $messages = MessageUser::where('receiver', $user->id)->get();
    for ($i = 0; $i < count($messages); $i++) {
        $messages[$i]["sender"] = User::find($messages[$i]["sender"])["login"];
    }
    logOut($messages);
    return json_encode(["status"=>"ok", "data"=>$messages]);
});

Route::post('/message/group/list', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    logOut(UserGroup::select("group")->where("user", $user->id)->distinct()->get());
    $groups = UserGroup::select("group")->where("user", $user->id)->distinct()->get();
    for($i = 0; $i < count($groups); $i++) {
        $groups[$i] = $groups[$i]["group"];
    }
    return json_encode(["status"=>"ok", "data"=>$groups]);
});

Route::post('/message/user/create', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('message')) {
        return json_encode(["status"=>"error", "description"=>"Message is required"]);
    }
    if (strlen($request->input('message')) < 0) {
        return json_encode(["status"=>"error", "description"=>"Message must be longer than 0 chars"]);
    }
    if (strlen($request->input('message')) > 200) {
        return json_encode(["status"=>"error", "description"=>"Message must be shorter than 200 chars"]);
    }
    if (!$request->has('receiver')) {
        return json_encode(["status"=>"error", "description"=>"Receiver is required"]);
    }
    $receiver = User::find($request->input("receiver"));
    if (empty($receiver)) {
        return json_encode(["status"=>"error", "description"=>"Receiver not exist"]);
    }
    if (!$request->has('isImage')) {
        return json_encode(["status"=>"error", "description"=>"IsImage is required"]);
    }
    return json_encode(["status"=>"ok"]);
});

Route::post('/message/group/create', function (Request $request) {
    $user = User::where('login', $request->input('login'))->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return json_encode(["status"=>"error", "description"=>"Wrong login or password"]);
    }
    if (!$request->has('message')) {
        return json_encode(["status"=>"error", "description"=>"Message is required"]);
    }
    if (strlen($request->input('message')) < 0) {
        return json_encode(["status"=>"error", "description"=>"Message must be longer than 0 chars"]);
    }
    if (strlen($request->input('message')) > 200) {
        return json_encode(["status"=>"error", "description"=>"Message must be shorter than 200 chars"]);
    }
    if (!$request->has('receiver')) {
        return json_encode(["status"=>"error", "description"=>"Receiver is required"]);
    }
    $receiver = User::find($request->input("receiver"));
    if (empty($receiver)) {
        return json_encode(["status"=>"error", "description"=>"Receiver not exist"]);
    }
    if (!$request->has('isImage')) {
        return json_encode(["status"=>"error", "description"=>"IsImage is required"]);
    }
    return json_encode(["status"=>"ok"]);
});

// TU JUŻ SĄ STARE ELEMENTY KODU

Route::post('/game/read', function (Request $request) { // dodać ile jest dopasować
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    return Game::all();
});

Route::post('/game/search', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('searchString'))) {
        return "SearchString is required";
    }
    if (empty($request->input('fullFilter'))) {
        return "Fullfilter is required";
    }
    $games = Game::where('name', 'like')
                    ->orWhere('description', 'like', '%' . $request->input('searchString') . '%')
                    ->orWhere('location', 'like', '%' . $request->input('searchString') . '%')
                    ->orWhere('time', 'like', '%' . $request->input('searchString') . '%')->get();
    if ((bool)$request->input('fullFilter')) {
        $result = [];
        foreach ($games as $g) {
            if (PlayerGamesMatch::where('game', $g->id)->count() < $g->people_counter) {
                array_push($result, $g);
            }
        }
        return $result;
    }
        return $games;
});

Route::post('/game/update', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('id'))) {
        return "Id is required";
    }
    if (positiveIntegerCheck($request->input('id'))) {
        return "Id is not positive integer";
    }
    if (empty($request->input('parameter'))) {
        return "Parameter is required";
    }
    if (empty($request->input('value'))) {
        return "Value is required";
    }
    $game = Game::find($request->input('id'));
    if (empty('game')) {
        return "Game is not exist";
    }
    if ($game->owner != $user->id) {
        return "Only creator can change game";
    }
    switch ($request->input('parameter')) {
        case 'name':
            if (strlen($request->input('value')) > 200) {
                return "Value is longer than 200 chars";
            }
            $game->name = $request->input('value');
            $game->save();
        break;
        case 'description':
            if (strlen($request->input('value')) > 2000) {
                return "Value is longer than 2000 chars";
            }
            $game->description = $request->input('value');
            $game->save();
        break;
        case 'location':
            if (strlen($request->input('value')) > 500) {
                return "Value is longer than 500 chars";
            }
            $game->name = $request->input('value');
            $game->save();
        break;
        case 'time':
            if (datetimeCheck($request->input('value'))) {
                return "Time format is invalid";
            }
            $game->time = $request->input('value');
            $game->save();
        break;
        case 'people_counter':
            if (positiveIntegerCheck($request->input('value'))) {
                return "People_counter is not positive integer";
            }
            $game->player_counter = $request->input('value');
            $game->save();
        break;
        default:
            return "Parameter is not exist";
    }
    return '1';
});

Route::post('/game/delete', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('id'))) {
        return "Id is required";
    }
    if (positiveIntegerCheck($request->input('id'))) {
        return "Id is not positive integer";
    }
    $game = Game::find($request->input('id'));
    if (empty($game)) {
        return "Game is not exist";
    }
    if ($game->owner != $user->id) {
        return "Only creator can delete game";
    }
    $game->delete();
    return '1';
});

Route::post('/group/create', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('name'))) {
        return "Name is required";
    }
    if (strlen($request->input('name')) > 200) {
        return "Name is longer than 200 chars";
    }
    if (Group::where('name', $request->input('name'))->where('owner', $user->id)->count() > 0) {
        return "This group is already exist";
    }
    $group = new Group;
    $group->name = $request->input('name');
    $group->owner = $user->id;
    $group->save();
    $UserGroup = new UserGroup;
    $UserGroup->player = $user->id;
    $UserGroup->role = 'admin';
    $UserGroup->group = $group->id;
    $UserGroup->save();
    return '1';
});

Route::post('/group/read', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    return UserGroup::where('player', $user->id)->get();
});

Route::post('/group/search', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('searchString'))) {
        return "SearchString is required";
    }
    $UserGroups = UserGroup::where('player', $user->id)->get();
    $result = [];
    foreach ($UserGroups as $pg) {
        array_push($result, Group::where('id', $pg->group)->first());
    }
    return $result;
});

Route::post('/group/update', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('id'))) {
        return "Id is required";
    }
    if (positiveIntegerCheck($request->input('id'))) {
        return "Id is not positive integer";
    }
    if (empty($request->input('name'))) {
        return "Name is required";
    }
    if (strlen($request->input('name')) > 200) {
        return "Name is longer than 200 chars";
    }
    $group = Group::find($request->input('id'));
    if (!empty($group)) {
        return "Group is not exist";
    }
    if ($group->owner != $user->id) {
        return "Only creator can change group";
    }
    $group->name = $request->input('name');
    $group->save();
    return '1';
});

Route::post('/group/add', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('id_player'))) {
        return "Id_player is required";
    }
    if (positiveIntegerCheck($request->input('id_player'))) {
        return "Id_player is not positive integer";
    }
    $player = Player::find($request->input('id_player'));
    if (empty($player)) {
        return "Player is not exist";
    }
    if (empty($request->input('id_group'))) {
        return "Id_group is required";
    }
    if (positiveIntegerCheck($request->input('id_group'))) {
        return "Id_group is not positive integer";
    }
    $group = Group::find($request->input('id_group'));
    if (empty($group)) {
        return "Group is not exist";
    }
    if ($group->owner != $user->id) {
        return "Only admin can add to group";
    }
    $UserGroup = new UserGroup;
    $UserGroup->player = $request->input('id_player');
    $UserGroup->role = 'default'; 
    $UserGroup->group = $request->input('id_group');
    $UserGroup->save();
    return '1';
});

Route::post('/group/remove', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('id_player'))) {
        return "Id_player is required";
    }
    if (positiveIntegerCheck($request->input('id_player'))) {
        return "Id_player is not positive integer";
    }
    $player = Player::find($request->input('id_player'));
    if (empty($player)) {
        return "Player is not exist";
    }
    if (empty($request->input('id_group'))) {
        return "Id_group is required";
    }
    if (positiveIntegerCheck($request->input('id_group'))) {
        return "Id_group is not positive integer";
    }
    $group = Group::find($request->input('id_group'));
    if (empty($group)) {
        return "Group is not exist";
    }
    if ($group->owner != $user->id) {
        return "Only admin can remove from group";
    }
    if ($request->input('id_player') == $user->id) {
        return "You can not remove self from your group";
    }
    $UserGroup = UserGroup::where('group', $request->input('id_group'))->where('player', $request->input('id_player'))->get();
    $UserGroup->delete();
    return '1';
});

Route::post('/group/delete', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('id'))) {
        return "Id is required";
    }
    if (positiveIntegerCheck($request->input('id'))) {
        return "Id is not positive integer";
    }
    $group = Group::find($request->input('id'));
    if (empty($group)) {
        return "Group is not exist";
    }
    if ($user->id != $group->owner) {
        return "Only admin can delete group";
    }
    $group->delete();
    return '1';
});

Route::post('/message-player/create', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('receiver'))) {
        return "Receiver is not defined";
    }
    if (positiveIntegerCheck($request->input('receiver'))) {
        return "Receiver is not positive integer";
    }
    if (empty($request->input('message'))) {
        return "Message is not defined";
    }
    if (strlen($request->input('message')) > 2000) {
        return "Message is longer than 2000 chars";
    }
    $MessageUser = new MessageUser;
    $MessageUser->sender = $user->id;
    $MessageUser->receiver = $request->input('receiver');
    $MessageUser->message = $request->input('message');
    $MessageUser->save();
    return '1';
});

Route::post('/message-player/read', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    return MessageUser::where('sender', $user->id)->orWhere('receiver', $user->id)->get();
});

Route::post('/message-group/create', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('receiver'))) {
        return "Receiver is not defined";
    }
    if (positiveIntegerCheck($request->input('receiver'))) {
        return "Receiver is not positive integer";
    }
    if (empty($request->input('message'))) {
        return "Message is not defined";
    }
    if (strlen($request->input('message')) > 2000) {
        return "Message is longer than 2000 chars";
    }
    $messageGroup = new MessageGroup;
    $messageGroup->sender = $user->id;
    $messageGroup->receiver = $request->input('receiver');
    $messageGroup->message = $request->input('message');
    $messageGroup->save();
    return '1';
});

Route::post('/message-group/read', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    return Message::where('sender', $user->id)->orWhere('receiver', $user->id)->get();
});

Route::post('/player/create', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (strlen($request->input('login')) > 200) {
        return "Login is longer than 200 chars";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    if (strlen($request->input('password')) < 20) {
        return "Password is shorter than 20 chars";
    }
    if (Player::where('login', $request->input('login'))->count() > 0) {
        return "Login is already taken";
    }
    $player = new Player;
    $player->login = $request->input('login');
    $player->password = hash('sha512', $request->input('password'));
    $player->save();
    return '1';
});

Route::post('/player/read', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    return Player::select('login')->get();
});

Route::post('/player/update', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('parameter'))) {
        return "Parameter is required";
    }
    if (empty($request->input('value'))) {
        return "Value is required";
    }
    switch ($request->input('parameter')) {
        case 'newLogin':
            if (strlen($request->input('newLlogin')) > 200) {
                return "NewLogin is longer than 200 chars";
            }
            $user->login = $request->input('newLlogin');
            $user->save();
        break;
        case 'newPassword':
            $user->password = hash('sha512', $request->input('newPassword'));
            $user->save();
        break;
        default:
            return "Parameter is not exist";
    }
    return '1';
});

Route::post('/player-game-match/create', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input('player'))) {
        return "Player is not defined";
    }
    if (positiveIntegerCheck($request->input('player'))) {
        return "Player is not positive integer";
    }
    if (empty($request->input('game'))) {
        return "Game is not defined";
    }
    if (positiveIntegerCheck($request->input('game'))) {
        return "Game is not positive integer";
    }
    $UserGameRole = new UserGameRole;
    $UserGameRole->player = $request->input('player');
    $UserGameRole->game = $request->input('game');
    $UserGameRole->save();
    return '1';
});

Route::post('/player-game-match/read', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input("game"))) {
        return "Game is not required";
    }
    if (positiveIntegerCheck($request->input("game"))) {
        return "Game is not positive integer";
    }
    return Player_game_match::where('game', $request->input("game"))->get();
});

Route::post('/player-game-match/delete', function (Request $request) {
    if (empty($request->input('login'))) {
        return "Login is required";
    }
    if (empty($request->input('password'))) {
        return "Password is required";
    }
    $user = Player::where('login', $request->input('login'))
                    ->where('password', hash('sha512', $request->input('password')))->first();
    if (empty($user)) {
        return "Wrong login or password";
    }
    if (empty($request->input("game"))) {
        return "Game is not required";
    }
    if (positiveIntegerCheck($request->input("game"))) {
        return "Game is not positive integer";
    }
    if (empty($request->input("player"))) {
        return "Player is not required";
    }
    if (positiveIntegerCheck($request->input("player"))) {
        return "Player is not positive integer";
    }
    $UserGameRole = UserGameRole::where('game', $request->input('game'))->where('player', $request->input('player'))->first();
    $UserGameRole->delete();
    return '1';
});

Route::fallback(function () {
    return "404 - bro :(";
});