@extends('layouts.app')

@section('title', 'Créer une Classe')

@section('content')
<div class="page-header">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Créer une Nouvelle Classe</h1>
    <div class="breadcrumb text-sm md:text-base">
        <a href="{{ route('administration.dashboard') }}">Tableau de bord</a> &raquo;
        <a href="{{ route('administration.classes.index') }}">Classes</a> &raquo;
        <span class="text-gray-600">Créer</span>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 md:px-6 py-4">
        <h3 class="text-white text-lg md:text-xl font-semibold flex items-center">
            <i class="fas fa-plus mr-2"></i>
            <span>Informations de la Classe</span>
        </h3>
    </div>

    <!-- Form -->
    <form action="{{ route('administration.classes.store') }}" method="POST" class="p-4 md:p-6 lg:p-8">
        @csrf

        <!-- Libellé -->
        <div class="mb-6">
            <label for="libelle" class="block text-sm font-medium text-gray-700 mb-2">
                Libellé de la classe <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="libelle"
                   name="libelle"
                   value="{{ old('libelle') }}"
                   required
                   maxlength="100"
                   placeholder="Ex: Licence 1 Informatique"
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base">
            @error('libelle')
                <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                Description
            </label>
            <textarea id="description"
                      name="description"
                      rows="4"
                      placeholder="Description optionnelle de la classe"
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base resize-y">{{ old('description') }}</textarea>
            @error('description')
                <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small>
            @enderror
        </div>

        <!-- Frais Section -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 md:p-6 mb-6">
            <h4 class="text-blue-700 font-semibold text-base md:text-lg mb-4 flex items-center">
                <i class="fas fa-euro-sign mr-2"></i>
                <span>Frais Associés</span>
            </h4>

            <!-- Frais - Responsive Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <!-- Frais d'inscription -->
                <div>
                    <label for="frais_inscription" class="block text-sm font-medium text-gray-700 mb-2">
                        Frais d'inscription <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <input type="number"
                               id="frais_inscription"
                               name="frais_inscription"
                               value="{{ old('frais_inscription', 0) }}"
                               required
                               min="0"
                               step="1"
                               placeholder="0"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base">
                        <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 text-sm font-medium">
                            FCFA
                        </span>
                    </div>
                    @error('frais_inscription')
                        <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Frais de mensualité -->
                <div>
                    <label for="frais_mensualite" class="block text-sm font-medium text-gray-700 mb-2">
                        Frais de mensualité <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <input type="number"
                               id="frais_mensualite"
                               name="frais_mensualite"
                               value="{{ old('frais_mensualite', 0) }}"
                               required
                               min="0"
                               step="1"
                               placeholder="0"
                               class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base">
                        <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 text-sm font-medium">
                            FCFA
                        </span>
                    </div>
                    @error('frais_mensualite')
                        <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small>
                    @enderror
                </div>
            </div>

            <!-- Frais de soutenance (pleine largeur) -->
            <div class="mt-4 md:mt-6">
                <label for="frais_soutenance" class="block text-sm font-medium text-gray-700 mb-2">
                    Frais de soutenance <span class="text-red-500">*</span>
                </label>
                <div class="flex max-w-md">
                    <input type="number"
                           id="frais_soutenance"
                           name="frais_soutenance"
                           value="{{ old('frais_soutenance', 0) }}"
                           required
                           min="0"
                           step="1"
                           placeholder="0"
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-base">
                    <span class="inline-flex items-center px-4 bg-gray-100 border border-l-0 border-gray-300 rounded-r-lg text-gray-600 text-sm font-medium">
                        FCFA
                    </span>
                </div>
                @error('frais_soutenance')
                    <small class="text-red-500 text-sm mt-1 block">{{ $message }}</small>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col-reverse sm:flex-row gap-3 justify-end pt-6 border-t border-gray-200">
            <a href="{{ route('administration.classes.index') }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors text-center">
                <i class="fas fa-times mr-2"></i>
                <span>Annuler</span>
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-save mr-2"></i>
                <span>Créer la classe</span>
            </button>
        </div>
    </form>
</div>
@endsection
