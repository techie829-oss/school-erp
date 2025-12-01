@extends('tenant.layouts.admin')

@section('title', $course->course_name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/lms/courses') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Courses</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ Str::limit($course->course_name, 20) }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Course Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                    {{ $course->course_name }}
                </h2>
                <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-6">
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $course->schoolClass->class_name }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        {{ $course->subject->subject_name }}
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $course->teacher->full_name }}
                    </div>
                </div>
            </div>
            <div class="mt-4 flex md:mt-0 md:ml-4">
                <a href="{{ url('/admin/lms/courses/' . $course->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Edit Course
                </a>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (Curriculum) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Curriculum Header -->
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Curriculum</h3>
                <button type="button" onclick="document.getElementById('add-chapter-modal').classList.remove('hidden')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Add Chapter
                </button>
            </div>

            <!-- Chapters List -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul role="list" class="divide-y divide-gray-200">
                    @forelse($course->chapters as $chapter)
                    <li>
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <p class="text-sm font-medium text-primary-600 truncate">
                                        Chapter {{ $chapter->chapter_number ?? $loop->iteration }}: {{ $chapter->chapter_name }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0 flex space-x-2">
                                    <button onclick="openEditChapterModal({{ $chapter->id }}, '{{ $chapter->chapter_name }}', '{{ $chapter->description }}')" class="text-gray-400 hover:text-gray-500">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ url('/admin/lms/chapters/' . $chapter->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this chapter?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="mt-2 sm:flex sm:justify-between">
                                <div class="sm:flex">
                                    <p class="flex items-center text-sm text-gray-500">
                                        {{ $chapter->description }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Topics -->
                            <div class="mt-4 border-t border-gray-100 pt-4">
                                <ul class="space-y-3">
                                    @foreach($chapter->topics as $topic)
                                    <li class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-md">
                                        <div class="flex items-center">
                                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.414l5 5a1 1 0 01.414 1.414V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ $topic->topic_name }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button onclick="openEditTopicModal({{ $topic->id }}, '{{ $topic->topic_name }}')" class="text-xs text-blue-600 hover:text-blue-800">Edit</button>
                                            <form action="{{ url('/admin/lms/topics/' . $topic->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete topic?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                                <div class="mt-3">
                                    <button onclick="openAddTopicModal({{ $chapter->id }})" class="text-sm text-primary-600 hover:text-primary-800 font-medium flex items-center">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Add Topic
                                    </button>
                                </div>
                            </div>
                        </div>
                    </li>
                    @empty
                    <li class="px-4 py-12 text-center">
                        <p class="text-gray-500">No chapters added yet.</p>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Sidebar (Stats & Actions) -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Course Stats</h3>
                <dl class="grid grid-cols-1 gap-4">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Students Enrolled</dt>
                        <dd class="text-sm font-medium text-gray-900">0</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Assignments</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $course->assignments->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Quizzes</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $course->quizzes->count() }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    <button onclick="document.getElementById('add-assignment-modal').classList.remove('hidden')" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Add Assignment
                    </button>
                    <button onclick="document.getElementById('add-quiz-modal').classList.remove('hidden')" class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Add Quiz
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals (Hidden by default) -->
<!-- Add Chapter Modal -->
<div id="add-chapter-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('add-chapter-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ url('/admin/lms/courses/' . $course->id . '/chapters') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add Chapter</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="chapter_name" class="block text-sm font-medium text-gray-700">Chapter Name</label>
                            <input type="text" name="chapter_name" id="chapter_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                    <button type="button" onclick="document.getElementById('add-chapter-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Topic Modal -->
<div id="add-topic-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('add-topic-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="add-topic-form" action="" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add Topic</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="topic_name" class="block text-sm font-medium text-gray-700">Topic Name</label>
                            <input type="text" name="topic_name" id="topic_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="video_url" class="block text-sm font-medium text-gray-700">Video URL (Optional)</label>
                            <input type="url" name="video_url" id="video_url" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                            <textarea name="content" id="content" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                    <button type="button" onclick="document.getElementById('add-topic-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Assignment Modal -->
<div id="add-assignment-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('add-assignment-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ url('/admin/lms/courses/' . $course->id . '/assignments') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add Assignment</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="assignment_title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="assignment_title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="assignment_description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="assignment_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
                                <input type="number" name="total_marks" id="total_marks" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date</label>
                                <input type="datetime-local" name="due_date" id="due_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="assignment_file" class="block text-sm font-medium text-gray-700">Attachment (Optional)</label>
                            <input type="file" name="file" id="assignment_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                    <button type="button" onclick="document.getElementById('add-assignment-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Quiz Modal -->
<div id="add-quiz-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('add-quiz-modal').classList.add('hidden')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ url('/admin/lms/courses/' . $course->id . '/quizzes') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Add Quiz</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="quiz_title" class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" name="title" id="quiz_title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="quiz_description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="quiz_description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="quiz_total_marks" class="block text-sm font-medium text-gray-700">Total Marks</label>
                                <input type="number" name="total_marks" id="quiz_total_marks" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="pass_marks" class="block text-sm font-medium text-gray-700">Pass Marks</label>
                                <input type="number" name="pass_marks" id="pass_marks" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (Minutes)</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="quiz_start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                                <input type="datetime-local" name="start_time" id="quiz_start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="quiz_end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="datetime-local" name="end_time" id="quiz_end_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">Add</button>
                    <button type="button" onclick="document.getElementById('add-quiz-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddTopicModal(chapterId) {
        const form = document.getElementById('add-topic-form');
        form.action = `/admin/lms/chapters/${chapterId}/topics`;
        document.getElementById('add-topic-modal').classList.remove('hidden');
    }

    function openEditTopicModal(topicId, topicName) {
        // TODO: Implement edit topic modal
        console.log('Edit topic:', topicId, topicName);
    }

    function openEditChapterModal(chapterId, chapterName, description) {
        // TODO: Implement edit chapter modal
        console.log('Edit chapter:', chapterId, chapterName, description);
    }
</script>
@endsection
