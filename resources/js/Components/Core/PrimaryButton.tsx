import { ButtonHTMLAttributes } from "react";

interface PrimaryButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  type?: "button" | "submit" | "reset";
}

export default function PrimaryButton({
  className = "",
  disabled,
  children,
  type = "submit", // ✅ default to submit
  ...props
}: PrimaryButtonProps) {
  return (
    <button
      {...props}
      type={type}
      className={`btn btn-primary ${className}`}
      disabled={disabled}
    >
      {children}
    </button>
  );
}
