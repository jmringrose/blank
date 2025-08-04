@extends('layouts.app')
@section('content')
    <div class="container max-w-5xl mx-auto text-neutral-content p-2">
        <div class="mt-4 w-full rounded text-2xl px-4">Test Form Colors</div>

        <div class="container mt-4 max-w-5xl mx-auto text-base bg-base-300 p-1 md:p-4 rounded-lg shadow-lg border border-base-300 b1">

        <!--regular tailwind -->
        <div class="align-left bg-info text-info-content rounded-lg shadow-lg p-2 mb-6 mt-6 w-[400px]">Regular Tailwind</div>
        <div class="flex text-zinc-700 ">
            <div class="p-4 border bg-info text-info-content border-gray-400 rounded-lg mr-4">Info</div>
            <div class="p-4 border bg-primary text-primary-content border-gray-400 rounded-lg mr-4">Primary</div>
            <div class="p-4 border bg-accent text-accent-content border-gray-400 rounded-lg mr-4">Accent</div>
            <div class="p-4 border bg-secondary text-secondary-content border-gray-400 rounded-lg mr-4">Secondary</div>
            <div class="p-4 border bg-neutral text-neutral-content border-gray-400 rounded-lg mr-4">Neutral</div>
            <div class="p-4 border bg-base-100 text-base-content border-gray-400 rounded-lg mr-4">Base 100</div>
            <div class="p-4 border bg-base-200 text-base-content border-gray-400 rounded-lg mr-4">Base 200</div>
            <div class="p-4 border bg-base-300 text-base-content border-gray-400 rounded-lg mr-4">Base 300</div>
        </div>
        <div class="mt-4 align-left bg-secondary text-secondary-content rounded-lg shadow-lg p-2 mb-6 w-[400px]">Daisy UI</div>
        <!-- buttons -->

        <div class="mb-4">
            <div class="flex flex-wrap">
                <button class="btn ml-2 mt-2 btn-active btn-primary">Primary</button>
                <button class="btn ml-2 mt-2 btn-active btn-secondary">Secondary</button>
                <button class="btn ml-2 mt-2 btn-active btn-accent">Accent</button>
                <button class="btn ml-2 mt-2 btn-active btn-info">Info</button>
                <button class="btn ml-2 mt-2 btn-active btn-success">Success</button>
                <button class="btn ml-2 mt-2 btn-active btn-warning">Warning</button>
                <button class="btn ml-2 mt-2 btn-active btn-error">Error</button>
            </div>
        </div>

        <!-- tab -->
        <tabdemo></tabdemo>

        <div tabindex="0" class="collapse collapse-plus bg-accent border-base-300 border">
            <div class="collapse-title font-semibold">How do I create an account?</div>
            <div class="collapse-content text-sm">
                Click the "Sign Up" button in the top right corner and follow the registration process.
            </div>
        </div>

        <!-- toggle, checkbox, radio -->
        <div class="p-4 border border-zinc-400 rounded-lg w-64 mt-3 bg-base-200">

            <input type="checkbox" class="mr-2 toggle toggle-primary"/>
            <input type="checkbox" class="mr-2 toggle toggle-error"/>
            <input type="checkbox" class="mr-2 toggle toggle-success"/>
            <br/>

            <input type="checkbox" class="mr-2 mt-2 mt-2 checkbox-primary checkbox"/>
            <input type="checkbox" class="mr-2 mt-2 mt-2 checkbox-secondary checkbox"/>
            <input type="checkbox" class="mr-2 mt-2 mt-2 checkbox-accent checkbox"/>
            <br/>

            <input type="radio" name="radio" class="mr-2 mt-2 mt-2 radio-primary radio"/>
            <input type="radio" name="radio" class="mr-2 mt-2 mt-2 radio-secondary radio"/>
            <input type="radio" name="radio" class="mr-2 mt-2 mt-2 radio-accent radio"/>



        </div>
        <!-- card -->
        <div class="card m-4 w-80 shadow-sm border border-base-300 bg-base-100">
            <figure>
                <img src="https://img.daisyui.com/images/blog/daisyui-5.webp"/>
            </figure>
            <div class="card-body bg-base-100 rounded-b-lg">
                <h2 class="card-title">DaisyUI 5.0</h2>
                <p>Rerum reiciendis beatae tenetur excepturi aut pariatur est eos. Sit sit necessitatibus.</p>
            </div>
        </div>



      <!-- dropdown -->
      <div class="flex">
          <details class="dropdown">
              <summary class="btn btn-primary m-1 w-64 mr-2 ">open/close dropdown</summary>
              <ul class="dropdown-content menu z-[2] w-52 rounded-box bg-base-200 p-2">
                  <li><a>Item 1</a></li>
                  <li><a>Item 2</a></li>
              </ul>
          </details>

        <!-- Open the modal using ID.showModal() method -->
        <button class="btn btn-primary mr-2 mt-1" onclick="my_modal_1.showModal()">open modal</button>
        <dialog id="my_modal_1" class="modal">
            <form method="dialog" class="modal-box">
                <p class="py-4">Press ESC key or click the button below to close</p>
                <div class="modal-action">
                    <button class="btn">Close</button>
                </div>
            </form>
        </dialog>

        <div class="drawer mb-6  mt-1">
            <input id="my-drawer" type="checkbox" class="drawer-toggle" />
            <div class="drawer-content">
                <!-- Page content here -->
                <label for="my-drawer" class="btn btn-primary drawer-button">Open left side drawer</label>
            </div>
            <div class="drawer-side">
                <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
                <ul class="menu bg-base-200 min-h-full w-80 p-4">
                    <!-- Sidebar content here -->
                    <li><a>Sidebar Item 1</a></li>
                    <li><a>Sidebar Item 2</a></li>
                    <li>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?"</li>
                </ul>
            </div>
        </div>
      </div>
        <!-- steps -->
        <ul class="steps my-4 w-full">
            <li class="step step-primary">Register</li>
            <li class="step step-primary">Choose plan</li>
            <li class="step step">Purchase</li>
            <li class="step">Receive Product</li>
        </ul>
    </div>
    </div>
@endsection
