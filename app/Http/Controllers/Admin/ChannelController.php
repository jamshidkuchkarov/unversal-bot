<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChannelRequest;
use App\Models\Channel;
use App\Support\AdminSchoolContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChannelController extends Controller
{
    public function __construct(private readonly AdminSchoolContext $schoolContext) {}

    public function index(): View
    {
        $school = $this->schoolContext->current(request()->user());

        return view('admin.channels.index', [
            'currentSchool' => $school,
            'availableSchools' => $this->schoolContext->schools(request()->user()),
            'channels' => Channel::query()->when($school, fn ($query) => $query->where('school_id', $school->id))->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.channels.form', ['channel' => new Channel()]);
    }

    public function store(ChannelRequest $request): RedirectResponse
    {
        $school = $this->schoolContext->current($request->user());
        abort_if(! $school, 404);

        Channel::query()->create([
            ...$request->validated(),
            'school_id' => $school->id,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.channels.index')->with('status', 'Kanal qo`shildi.');
    }

    public function edit(Channel $channel): View
    {
        $this->schoolContext->authorizeModel(request()->user(), $channel);

        return view('admin.channels.form', compact('channel'));
    }

    public function update(ChannelRequest $request, Channel $channel): RedirectResponse
    {
        $this->schoolContext->authorizeModel($request->user(), $channel);

        $channel->update([
            ...$request->validated(),
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.channels.index')->with('status', 'Kanal yangilandi.');
    }

    public function destroy(Channel $channel): RedirectResponse
    {
        $this->schoolContext->authorizeModel(request()->user(), $channel);

        $channel->delete();

        return back()->with('status', 'Kanal o`chirildi.');
    }

    public function getChatId(Request $request): JsonResponse
    {
        $username = $request->input('username');

        if (!$username) {
            return response()->json([
                'success' => false,
                'message' => 'Username kiriting',
            ], 400);
        }

        $school = $this->schoolContext->current($request->user());

        if (!$school || !$school->bot) {
            return response()->json([
                'success' => false,
                'message' => 'Bot topilmadi',
            ], 404);
        }

        try {
            config(['telegram.bots.mybot.token' => $school->bot->bot_token]);

            $response = Telegram::getChat(['chat_id' => '@' . ltrim($username, '@')]);

            return response()->json([
                'success' => true,
                'chat_id' => $response->id,
                'title' => $response->title,
                'username' => $response->username ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kanal topilmadi. Bot kanalda admin ekanligiga ishonch hosil qiling.',
            ], 404);
        }
    }
}
