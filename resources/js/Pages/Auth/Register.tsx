import InputError from "@/Components/Core/InputError";
import InputLabel from "@/Components/Core/InputLabel";
import PrimaryButton from "@/Components/Core/PrimaryButton";
import TextInput from "@/Components/Core/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { FormEventHandler, useState } from "react";

export default function Register() {
  const { data, setData, post, processing, errors, reset } = useForm({
    name: "",
    email: "",
    phone: "",
    address: "",
    city: "",
    state: "",
    postal_code: "",
    country: "",
    password: "",
    password_confirmation: "",
  });

  const [currentStep, setCurrentStep] = useState(1);
  const totalSteps = 2;

  const validateStep1 = () => {
    if (!data.name) return false;
    if (!data.email) return false;
    if (!data.password) return false;
    if (!data.password_confirmation) return false;
    if (data.password !== data.password_confirmation) return false;

    return true;
  };

  const nextStep = () => {
    if (currentStep === 1 && validateStep1()) {
      setCurrentStep(currentStep + 1);
    }
  };

  const prevStep = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1);
    }
  };

  const submit: FormEventHandler = (e) => {
    e.preventDefault();

    console.log("Submitting data:", data);
    post(route("register"), {
      onFinish: () => reset("password", "password_confirmation"),
    });
  };

  return (
    <GuestLayout>
      <Head title="Register" />

      <div className="card bg-white shadow max-w-[500px] mx-auto">
        <div className="card-body">
          <div className="mb-6">
            <h2 className="text-xl font-semibold text-center">
              Create an Account
            </h2>
            <div className="flex justify-between mt-4">
              {Array.from({ length: totalSteps }).map((_, index) => (
                <div
                  key={index}
                  className={`flex-1 h-2 mx-1 rounded-full ${
                    currentStep > index
                      ? "bg-primary"
                      : currentStep === index + 1
                      ? "bg-secondary"
                      : "bg-gray-200"
                  }`}
                />
              ))}
            </div>
            <p className="text-center text-sm text-gray-500 mt-2">
              Step {currentStep} of {totalSteps}
            </p>
          </div>

          <form onSubmit={submit}>
            {currentStep === 1 && (
              <div className="space-y-4">
                <div>
                  <InputLabel
                    htmlFor="name"
                    value="Name"
                    className="text-gray-700"
                  />
                  <TextInput
                    id="name"
                    name="name"
                    value={data.name}
                    className="mt-1 block w-full"
                    autoComplete="name"
                    isFocused={true}
                    onChange={(e) => setData("name", e.target.value)}
                    required
                  />
                  <InputError message={errors.name} className="mt-2" />
                </div>

                <div>
                  <InputLabel
                    htmlFor="email"
                    value="Email"
                    className="text-gray-700"
                  />
                  <TextInput
                    id="email"
                    type="email"
                    name="email"
                    value={data.email}
                    className="mt-1 block w-full"
                    autoComplete="username"
                    onChange={(e) => setData("email", e.target.value)}
                    required
                  />
                  <InputError message={errors.email} className="mt-2" />
                </div>

                <div>
                  <InputLabel
                    htmlFor="password"
                    value="Password"
                    className="text-gray-700"
                  />
                  <TextInput
                    id="password"
                    type="password"
                    name="password"
                    value={data.password}
                    className="mt-1 block w-full"
                    autoComplete="new-password"
                    onChange={(e) => setData("password", e.target.value)}
                    required
                  />
                  <InputError message={errors.password} className="mt-2" />
                </div>

                <div>
                  <InputLabel
                    htmlFor="password_confirmation"
                    value="Confirm Password"
                    className="text-gray-700"
                  />
                  <TextInput
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    value={data.password_confirmation}
                    className="mt-1 block w-full"
                    autoComplete="new-password"
                    onChange={(e) =>
                      setData("password_confirmation", e.target.value)
                    }
                    required
                  />
                  <InputError
                    message={errors.password_confirmation}
                    className="mt-2"
                  />
                </div>
              </div>
            )}

            {currentStep === 2 && (
              <div className="space-y-4">
                <div>
                  <InputLabel
                    htmlFor="phone"
                    value="Phone"
                    className="text-gray-700"
                  />
                  <TextInput
                    id="phone"
                    type="tel"
                    name="phone"
                    value={data.phone}
                    className="mt-1 block w-full"
                    autoComplete="tel"
                    onChange={(e) => setData("phone", e.target.value)}
                  />
                  <InputError message={errors.phone} className="mt-2" />
                </div>

                <div>
                  <InputLabel
                    htmlFor="address"
                    value="Address"
                    className="text-gray-700"
                  />
                  <TextInput
                    id="address"
                    name="address"
                    value={data.address}
                    className="mt-1 block w-full"
                    autoComplete="street-address"
                    onChange={(e) => setData("address", e.target.value)}
                  />
                  <InputError message={errors.address} className="mt-2" />
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <InputLabel
                      htmlFor="city"
                      value="City"
                      className="text-gray-700"
                    />
                    <TextInput
                      id="city"
                      name="city"
                      value={data.city}
                      className="mt-1 block w-full"
                      autoComplete="address-level2"
                      onChange={(e) => setData("city", e.target.value)}
                    />
                    <InputError message={errors.city} className="mt-2" />
                  </div>

                  <div>
                    <InputLabel
                      htmlFor="state"
                      value="State/Province"
                      className="text-gray-700"
                    />
                    <TextInput
                      id="state"
                      name="state"
                      value={data.state}
                      className="mt-1 block w-full"
                      autoComplete="address-level1"
                      onChange={(e) => setData("state", e.target.value)}
                    />
                    <InputError message={errors.state} className="mt-2" />
                  </div>
                </div>

                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <InputLabel
                      htmlFor="postal_code"
                      value="Postal Code"
                      className="text-gray-700"
                    />
                    <TextInput
                      id="postal_code"
                      name="postal_code"
                      value={data.postal_code}
                      className="mt-1 block w-full"
                      autoComplete="postal-code"
                      onChange={(e) => setData("postal_code", e.target.value)}
                    />
                    <InputError message={errors.postal_code} className="mt-2" />
                  </div>

                  <div>
                    <InputLabel
                      htmlFor="country"
                      value="Country"
                      className="text-gray-700"
                    />
                    <TextInput
                      id="country"
                      name="country"
                      value={data.country}
                      className="mt-1 block w-full"
                      autoComplete="country-name"
                      onChange={(e) => setData("country", e.target.value)}
                    />
                    <InputError message={errors.country} className="mt-2" />
                  </div>
                </div>
              </div>
            )}

            <div className="mt-6 flex items-center justify-between">
              <div className="flex space-x-4">
                {currentStep > 1 && (
                  <button
                    type="button"
                    onClick={prevStep}
                    className="btn btn-outline"
                  >
                    Back
                  </button>
                )}

                {currentStep < totalSteps ? (
                  <button
                    type="button"
                    onClick={nextStep}
                    className="btn btn-primary"
                    disabled={!validateStep1()}
                  >
                    Next Step
                  </button>
                ) : (
                  <PrimaryButton type="submit" disabled={processing}>
                    Register
                  </PrimaryButton>
                )}
              </div>

              <Link
                href={route("login")}
                className="text-sm text-primary hover:underline"
              >
                Already registered?
              </Link>
            </div>
          </form>
        </div>
      </div>
    </GuestLayout>
  );
}
