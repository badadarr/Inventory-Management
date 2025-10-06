<?php

namespace App\Http\Controllers;

use App\Enums\Core\FilterFieldTypeEnum;
use App\Enums\Core\SortOrderEnum;
use App\Enums\User\UserFiltersEnum;
use App\Enums\User\UserRoleEnum;
use App\Enums\User\UserSortFieldsEnum;
use App\Helpers\BaseHelper;
use App\Http\Requests\User\UserIndexRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private readonly UserService $service)
    {
    }

    public function index(UserIndexRequest $request)
    {
        if ($request->inertia == "disabled") {
            $query = $request->validated();
            $query["sort_by"] = UserSortFieldsEnum::NAME->value;
            return $this->service->getAll($query);
        }

        return Inertia::render(
            component: 'User/Index',
            props: [
                'users' => $this->service->getAll($request->validated()),
                'filters'   => [
                    UserFiltersEnum::NAME->value  => [
                        'label'       => UserFiltersEnum::NAME->label(),
                        'placeholder' => 'Enter name.',
                        'type'        => FilterFieldTypeEnum::STRING->value,
                        'value'       => $request->validated()[UserFiltersEnum::NAME->value] ?? "",
                    ],
                    UserFiltersEnum::EMAIL->value => [
                        'label'       => UserFiltersEnum::EMAIL->label(),
                        'placeholder' => 'Enter email.',
                        'type'        => FilterFieldTypeEnum::STRING->value,
                        'value'       => $request->validated()[UserFiltersEnum::EMAIL->value] ?? "",
                    ],
                    "sort_by"                     => [
                        'label'       => 'Sort By',
                        'placeholder' => 'Select a sort field',
                        'type'        => FilterFieldTypeEnum::SELECT_STATIC->value,
                        'value'       => $request->validated()['sort_by'] ?? "",
                        'options'     => BaseHelper::convertKeyValueToLabelValueArray(UserSortFieldsEnum::choices()),
                    ],
                    "sort_order"                  => [
                        'label'       => 'Sort order',
                        'placeholder' => 'Select a sort order',
                        'type'        => FilterFieldTypeEnum::SELECT_STATIC->value,
                        'value'       => $request->validated()['sort_order'] ?? "",
                        'options'     => BaseHelper::convertKeyValueToLabelValueArray(SortOrderEnum::choices()),
                    ]
                ],
                'roles' => UserRoleEnum::choices(),
            ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(UserRoleEnum::values())],
            'company_name' => 'nullable|string|max:255',
            'company_id' => 'nullable|integer',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('flash', [
            'isSuccess' => true,
            'message' => 'User created successfully.'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(UserRoleEnum::values())],
            'company_name' => 'nullable|string|max:255',
            'company_id' => 'nullable|integer',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('flash', [
            'isSuccess' => true,
            'message' => 'User updated successfully.'
        ]);
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('flash', [
                'isSuccess' => false,
                'message' => 'You cannot delete your own account.'
            ]);
        }

        $user->delete();

        return redirect()->route('users.index')->with('flash', [
            'isSuccess' => true,
            'message' => 'User deleted successfully.'
        ]);
    }
}
