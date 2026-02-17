<?php

namespace App\Livewire;

use App\Models\CaptivePortalTemplate;
use App\Models\Router;
use Livewire\Component;
use Livewire\WithFileUploads;

class TemplateEditor extends Component
{
    use WithFileUploads;

    public $templates = [];
    public $selectedTemplate = null;
    public $showModal = false;
    public $isEditing = false;

    // Form fields
    public $name = '';
    public $router_id = '';
    public $background_color = '#1e3a5f';
    public $primary_color = '#3b82f6';
    public $text_color = '#ffffff';
    public $font_family = 'Poppins';
    public $base_font_size = 16;
    public $heading_font_size = 24;
    public $button_radius = 12;
    public $package_card_radius = 16;
    public $package_card_shadow = true;
    public $package_grid_sm = 2;
    public $package_grid_md = 2;
    public $package_grid_lg = 3;
    public $package_card_bg = '#ffffff';
    public $package_card_text = '#0f172a';
    public $cta_button_text = 'প্যাকেজ সিলেক্ট করুন';
    public $cta_button_color = '#3b82f6';
    public $cta_button_text_color = '#ffffff';
    public $welcome_title = 'স্বাগতম!';
    public $welcome_message = 'আপনার পছন্দের প্যাকেজ সিলেক্ট করুন';
    public $footer_text = '';
    public $terms_conditions = '';
    public $custom_css = '';
    public $custom_js = '';
    public $show_packages = true;
    public $require_phone = true;
    public $require_email = false;
    public $is_active = true;
    public $is_default = false;
    public $payment_methods = ['bkash', 'nagad'];

    public $logo;
    public $background_image;

    protected $rules = [
        'name' => 'required|string|max:255',
        'router_id' => 'nullable|exists:routers,id',
        'background_color' => 'required|string|max:20',
        'primary_color' => 'required|string|max:20',
        'text_color' => 'required|string|max:20',
        'font_family' => 'nullable|string|max:50',
        'base_font_size' => 'nullable|integer|min:12|max:20',
        'heading_font_size' => 'nullable|integer|min:18|max:36',
        'button_radius' => 'nullable|integer|min:6|max:32',
        'package_card_radius' => 'nullable|integer|min:8|max:32',
        'package_card_shadow' => 'boolean',
        'package_grid_sm' => 'nullable|integer|min:1|max:3',
        'package_grid_md' => 'nullable|integer|min:1|max:3',
        'package_grid_lg' => 'nullable|integer|min:1|max:4',
        'package_card_bg' => 'nullable|string|max:20',
        'package_card_text' => 'nullable|string|max:20',
        'cta_button_text' => 'nullable|string|max:50',
        'cta_button_color' => 'nullable|string|max:20',
        'cta_button_text_color' => 'nullable|string|max:20',
        'welcome_title' => 'nullable|string|max:500',
        'welcome_message' => 'nullable|string',
        'footer_text' => 'nullable|string|max:500',
        'terms_conditions' => 'nullable|string',
        'custom_css' => 'nullable|string',
        'custom_js' => 'nullable|string',
        'logo' => 'nullable|image|max:2048',
        'background_image' => 'nullable|image|max:5120',
    ];

    public function mount()
    {
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        $this->templates = CaptivePortalTemplate::with('router')->get();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->showModal = true;
    }

    public function editTemplate($id)
    {
        $template = CaptivePortalTemplate::find($id);
        if (!$template) return;

        $this->selectedTemplate = $template;
        $this->isEditing = true;

        $this->name = $template->name;
        $this->router_id = $template->router_id;
        $this->background_color = $template->background_color;
        $this->primary_color = $template->primary_color;
        $this->text_color = $template->text_color;
        $this->font_family = $template->font_family ?? 'Poppins';
        $this->base_font_size = $template->base_font_size ?? 16;
        $this->heading_font_size = $template->heading_font_size ?? 24;
        $this->button_radius = $template->button_radius ?? 12;
        $this->package_card_radius = $template->package_card_radius ?? 16;
        $this->package_card_shadow = $template->package_card_shadow ?? true;
        $this->package_grid_sm = $template->package_grid_sm ?? 2;
        $this->package_grid_md = $template->package_grid_md ?? 2;
        $this->package_grid_lg = $template->package_grid_lg ?? 3;
        $this->package_card_bg = $template->package_card_bg ?? '#ffffff';
        $this->package_card_text = $template->package_card_text ?? '#0f172a';
        $this->cta_button_text = $template->cta_button_text ?? 'প্যাকেজ সিলেক্ট করুন';
        $this->cta_button_color = $template->cta_button_color ?? '#3b82f6';
        $this->cta_button_text_color = $template->cta_button_text_color ?? '#ffffff';
        $this->welcome_title = $template->welcome_title;
        $this->welcome_message = $template->welcome_message;
        $this->footer_text = $template->footer_text;
        $this->terms_conditions = $template->terms_conditions;
        $this->custom_css = $template->custom_css;
        $this->custom_js = $template->custom_js;
        $this->show_packages = $template->show_packages;
        $this->require_phone = $template->require_phone;
        $this->require_email = $template->require_email;
        $this->is_active = $template->is_active;
        $this->is_default = $template->is_default;
        $this->payment_methods = $template->payment_methods ?? ['bkash', 'nagad'];

        $this->showModal = true;
    }

    public function saveTemplate()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'router_id' => $this->router_id ?: null,
            'background_color' => $this->background_color,
            'primary_color' => $this->primary_color,
            'text_color' => $this->text_color,
            'font_family' => $this->font_family,
            'base_font_size' => $this->base_font_size,
            'heading_font_size' => $this->heading_font_size,
            'button_radius' => $this->button_radius,
            'package_card_radius' => $this->package_card_radius,
            'package_card_shadow' => $this->package_card_shadow,
            'package_grid_sm' => $this->package_grid_sm,
            'package_grid_md' => $this->package_grid_md,
            'package_grid_lg' => $this->package_grid_lg,
            'package_card_bg' => $this->package_card_bg,
            'package_card_text' => $this->package_card_text,
            'cta_button_text' => $this->cta_button_text,
            'cta_button_color' => $this->cta_button_color,
            'cta_button_text_color' => $this->cta_button_text_color,
            'welcome_title' => $this->welcome_title,
            'welcome_message' => $this->welcome_message,
            'footer_text' => $this->footer_text,
            'terms_conditions' => $this->terms_conditions,
            'custom_css' => $this->custom_css,
            'custom_js' => $this->custom_js,
            'show_packages' => $this->show_packages,
            'require_phone' => $this->require_phone,
            'require_email' => $this->require_email,
            'is_active' => $this->is_active,
            'payment_methods' => $this->payment_methods,
        ];

        // Handle logo upload
        if ($this->logo) {
            $data['logo_path'] = $this->logo->store('templates', 'public');
        }

        // Handle background image upload
        if ($this->background_image) {
            $data['background_image'] = $this->background_image->store('templates', 'public');
        }

        if ($this->isEditing && $this->selectedTemplate) {
            $this->selectedTemplate->update($data);

            if ($this->is_default) {
                $this->selectedTemplate->setAsDefault();
            }

            session()->flash('success', 'টেমপ্লেট আপডেট হয়েছে।');
        } else {
            $template = CaptivePortalTemplate::create($data);

            if ($this->is_default) {
                $template->setAsDefault();
            }

            session()->flash('success', 'নতুন টেমপ্লেট তৈরি হয়েছে।');
        }

        $this->closeModal();
        $this->loadTemplates();
    }

    public function deleteTemplate($id)
    {
        $template = CaptivePortalTemplate::find($id);
        if ($template && !$template->is_default) {
            $template->delete();
            session()->flash('success', 'টেমপ্লেট ডিলিট হয়েছে।');
            $this->loadTemplates();
        } else {
            session()->flash('error', 'ডিফল্ট টেমপ্লেট ডিলিট করা যাবে না।');
        }
    }

    public function setDefault($id)
    {
        $template = CaptivePortalTemplate::find($id);
        if ($template) {
            $template->setAsDefault();
            session()->flash('success', 'ডিফল্ট টেমপ্লেট সেট হয়েছে।');
            $this->loadTemplates();
        }
    }

    public function previewTemplate($id)
    {
        return redirect()->route('captive.preview', ['template' => $id]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'router_id', 'welcome_title', 'welcome_message',
            'footer_text', 'terms_conditions', 'custom_css', 'custom_js',
            'logo', 'background_image', 'selectedTemplate'
        ]);
        $this->background_color = '#1e3a5f';
        $this->primary_color = '#3b82f6';
        $this->text_color = '#ffffff';
        $this->font_family = 'Poppins';
        $this->base_font_size = 16;
        $this->heading_font_size = 24;
        $this->button_radius = 12;
        $this->package_card_radius = 16;
        $this->package_card_shadow = true;
        $this->package_grid_sm = 2;
        $this->package_grid_md = 2;
        $this->package_grid_lg = 3;
        $this->package_card_bg = '#ffffff';
        $this->package_card_text = '#0f172a';
        $this->cta_button_text = 'প্যাকেজ সিলেক্ট করুন';
        $this->cta_button_color = '#3b82f6';
        $this->cta_button_text_color = '#ffffff';
        $this->show_packages = true;
        $this->require_phone = true;
        $this->require_email = false;
        $this->is_active = true;
        $this->is_default = false;
        $this->payment_methods = ['bkash', 'nagad'];
    }

    public function render()
    {
        $routers = Router::where('is_active', true)->get();

        return view('livewire.template-editor', [
            'routers' => $routers,
        ]);
    }
}
